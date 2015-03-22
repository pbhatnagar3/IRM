close all force
clear all,clear classes,clc
%----------------------------------------------------%
% Database
host     = 'localhost';
user     = 'root';
password = 'csip';
dbName   = 'its';

% JDBC Parameters
jdbcString = sprintf('jdbc:mysql://%s/%s', host, dbName);
jdbcDriver = 'com.mysql.jdbc.Driver';

% Set this to the path to your MySQL Connector/J JAR
%javaaddpath('/home/gte269x/mysql-connector-java-5.1.18/mysql-connector-java-5.1.18-bin.jar')

% Create the database connection object
dbConn = database(dbName, user , password, jdbcDriver, jdbcString);

% Check to make sure that we successfully connected
if isconnection(dbConn) 
    tic%begin stopwatch
    exec(dbConn, 'use its;');

 %select the users by semester
users=exec(dbConn,['select id from users where status=''Fall_2011'' or status=''Spring_2012'' or status=''Summer_2012'' or status=''Fall_2012'' or status=''Spring_2013'';']);
%users=exec(dbConn,['select id from users where status=''Summer_2012'' or status=''Fall_2012'';']);
    users=fetch(users);
    users=users.Data;
    users=users';
    scores=[];
    duration=[];
    IDs=[];
    ratings=[];
    skip=[];    
      
    for i=1:length(users)
%select scores,durations, and ratings from answers that have both a score
%and a duration
        tempscores=exec(dbConn,['select score from stats_',num2str(users{i}),' where duration is not null;']);
        %tempscores=exec(dbConn,['select score from stats_',num2str(users(i)),' where duration and score is not null;']);
        tempscores=fetch(tempscores);
        tempscores=(tempscores.Data)';
        tempduration=exec(dbConn,['select duration from stats_',num2str(users{i}),' where duration is not null;']);
        %tempduration=exec(dbConn,['select duration from stats_',num2str(users(i)),' where duration and score is not null;']);
        tempduration=fetch(tempduration);
        tempduration=(tempduration.Data)';
        tempIDs=exec(dbConn,['select question_id from stats_',num2str(users{i}),' where duration is not null;']);
        %tempIDs=exec(dbConn,['select question_id from stats_',num2str(users(i)),' where duration and score is not null;']);
        tempIDs=fetch(tempIDs);
        tempIDs=(tempIDs.Data)';
        temprating=exec(dbConn,['select rating from stats_',num2str(users{i}),' where duration is not null;']);
        temprating=fetch(temprating);
        temprating=(temprating.Data)';
        
        %fetch skips
        tempskip=exec(dbConn,['select event from stats_',num2str(users{i}),' where duration is not null;']);
        tempskip=fetch(tempskip);
        tempskip=(tempskip.Data)';
        
        if(iscell(temprating))
            if strcmp(temprating{1},'No Data')==0
                ratings=[ratings temprating{:}];
            end
        end%these if statements take care of some issues that can cause buggy behavior (empty data, etc.)
        
        if(iscell(tempIDs))
        if strcmp(tempIDs{1},'No Data')==0
            IDs=[IDs tempIDs{:}];
        end
        end
        
        if(iscell(tempscores))
        if strcmp(tempscores{1},'No Data')==0
            scores=[scores tempscores{:}];
        end
        end
        
       if(iscell(tempduration))
        if strcmp(tempduration{1},'No Data')==0
            duration=[duration tempduration{:}];
        end
       end
       
        if(iscell(tempskip))
        if strcmp(tempskip{1},'No Data')==0
            %skip=[skip tempskip{:}];
            for i=1:length(tempskip)
                if strcmpi(tempskip{i},'chapter')==1
                    skip=[skip 0];%0 indicates it WASN'T skipped
                elseif strcmpi(tempskip{i},'null')==1
                    skip=[skip 2];%2 indicates nothing was recorded
                elseif strcmpi(tempskip{i},'skip')==1
                    skip=[skip 1];%1 indicates it WAS skipped
                else skip=[skip 4];%catch all, something wrong
                end
            end
        end
        end

    end
    
    %select a question type
    uniqueIDs=unique(IDs);
%     type={};
    for i=1:length(uniqueIDs)
        check=exec(dbConn,['select qtype from questions where id=',num2str(uniqueIDs(i)),';']);
        temp=fetch(check);
        type(i)=temp.Data;
    end
%     %comment this out if we want all questions
    for i=1:length(type)
        if(strcmpi('mc',type{i})==0)
         
            duration(find(uniqueIDs(i)==IDs))=[];
            scores(find(uniqueIDs(i)==IDs))=[];
            ratings(find(uniqueIDs(i)==IDs))=[];
            skip(find(uniqueIDs(i)==IDs))=[];
            IDs(find(uniqueIDs(i)==IDs))=[];
        
            uniqueIDs(i)=-5;
            type{i}=0;%set type to 0 for later removal
        end
    end
    uniqueIDs(find(uniqueIDs==-5))=[];
    %how to remove question types from type?
    

    %end selecting a question type
    
%     highthreshold=480;%ignore samples with longer duration than this
%     eliminate=find(duration>highthreshold);
%    duration(eliminate)=[];%get rid of stuff with super long duration
%     scores(eliminate)=[];
%     IDs(eliminate)=[];
%    lowthreshold=3;%ignore samples with shorter duration than this
%     eliminate2=find(duration<=lowthreshold);
%    duration(eliminate2)=[];
%    scores(eliminate2)=[];
%     IDs(eliminate2)=[];
%     uniqueIDs=unique(IDs);%unique/distinct questions

    
%    for i=1:length(uniqueIDs)
%        a=find(uniqueIDs(i)==IDs);
%        if(length(a)<15)%if a question was answered <15 times, throw it out
%            uniqueIDs(i)=-5;
%            IDs(a)=[];
%            scores(a)=[];
%            duration(a)=[];   
%            ratings(a)=[];
%        end
%    end
%    uniqueIDs(find(uniqueIDs==-5))=[];
%BELOW is the code used for the SQL table MinedDataV1
%in cell array format so that it could be used for .csv or .xls easily
%commented out for when data mining
% 

% for i=1:length(scores) %not needed? just nanmean?
%     if(isnumeric(scores(i))==0)%if score is null (skipped Q)
%         scores(i)=NaN;%set that score to NaN to simplify future processing
%     end
% end

 exec(dbConn,['delete from MinedDataMC;']);
 %exec(dbConn,['delete from MinedData;']);
   mastercell={};
   mastercell{1,1}='ID';
   mastercell{1,2}='Question ID';
   mastercell{1,3}='qtype';
   mastercell{1,4}='Avg. Score';
   mastercell{1,5}='% Zero';
   mastercell{1,6}='% Perfect';
   mastercell{1,7}='# Zero';
   mastercell{1,8}='# Perfect';
   mastercell{1,9}='Avg. Duration';
   mastercell{1,10}='# Answers';
   mastercell{1,11}='Avg. Rating (please note that there are very few ratings)';
   mastercell{1,12}='# Ratings';
   mastercell{1,13}='NumSkips';
   
   test=[];
   for i=1:length(uniqueIDs)
       test=[test i];
       mastercell{i+1,1}=i;
       mastercell{i+1,2}=uniqueIDs(i);
       %mastercell{i+1,3}=type{i};
       mastercell{i+1,3}='mc';
       %use the above for when using a SPECIFIC type
       mastercell{i+1,4}=nanmean(scores(find(uniqueIDs(i)==IDs)));
       mastercell{i+1,5}=100*length(find(scores(find(uniqueIDs(i)==IDs))==0))/length(find(uniqueIDs(i)==IDs));
       mastercell{i+1,6}=100*length(find(scores(find(uniqueIDs(i)==IDs))==100))/length(find(uniqueIDs(i)==IDs));
       mastercell{i+1,7}=length(find(scores(find(uniqueIDs(i)==IDs))==0));
       mastercell{i+1,8}=length(find(scores(find(uniqueIDs(i)==IDs))==100));
       mastercell{i+1,9}=mean(duration(find(uniqueIDs(i)==IDs)));%INCLUDES skips
       mastercell{i+1,10}=length(find(uniqueIDs(i)==IDs));
       if(isnan(nanmean(ratings(find(uniqueIDs(i)==IDs))))==0)
       mastercell{i+1,11}=nanmean(ratings(find(uniqueIDs(i)==IDs)));%does NOT include skips
       else mastercell{i+1,11}=0;
       end
       tempratings=ratings(find(uniqueIDs(i)==IDs));
       tempratings(isnan(tempratings))=[];
       mastercell{i+1,12}=length(tempratings);
       mastercell{i+1,13}=length(find(skip(find(uniqueIDs(i)==IDs))==1));
    
       check=exec(dbConn,['insert into MinedDataMC values (',num2str(mastercell{i+1,1}),',',num2str(mastercell{i+1,2}),',''',mastercell{i+1,3},''',',num2str(mastercell{i+1,4}),',',num2str(mastercell{i+1,5}),',',num2str(mastercell{i+1,6}),',',num2str(mastercell{i+1,7}),',',num2str(mastercell{i+1,8}),',',num2str(mastercell{i+1,9}),',',num2str(mastercell{i+1,10}),',',num2str(mastercell{i+1,11}),',',num2str(mastercell{i+1,12}),',',num2str(mastercell{i+1,13}),');']);
       %check=exec(dbConn,['insert into MinedData values (',num2str(mastercell{i+1,1}),',',num2str(mastercell{i+1,2}),',''',mastercell{i+1,3},''',',num2str(mastercell{i+1,4}),',',num2str(mastercell{i+1,5}),',',num2str(mastercell{i+1,6}),',',num2str(mastercell{i+1,7}),',',num2str(mastercell{i+1,8}),',',num2str(mastercell{i+1,9}),',',num2str(mastercell{i+1,10}),',',num2str(mastercell{i+1,11}),',',num2str(mastercell{i+1,12}),',',num2str(mastercell{i+1,13}),');']);
   end
% exec(dbConn,['delete MinedDataV1;']);   
% 
%this code can be easily modified for use with a specific question
%but is currently designed to graph avg. duration to obtain a score
%for a question, then loop through all questions and plot them all on the
%same axes
% x=length(uniqueIDs);
% dur=[];
% durationsfinal=[];
% scoresfinal=[];
% figure(1);
% for k=1:x
%    sample=uniqueIDs(k);
%     samplingat=find(IDs==sample);
%     if(length(samplingat)<10) %%if <10 samples, ignore this question 
%         continue %
%     end%
%     sampledur=duration(samplingat);%find durations that are fr that q
%     samplescores=scores(samplingat);%and scores
%     uniquescores=unique(samplescores);
%     uniquedurs=unique(sampledur);
%     scoresfinal=[scoresfinal uniquescores];
%   dur=[];
%     for i=1:length(uniquescores)
%         dur(i)=mean(sampledur(find(samplescores==uniquescores(i))));
%         durationsfinal=[durationsfinal dur(i)];
%     end
%     %figure(k);
%    % figure(1);
% scatter(dur,uniquescores)
% hold on;
% %  title(['scores vs duration for unique question ',num2str(k),'']);
%   title('scores vs duration for unique question w/ all questions on graph');
% end
% uniquescoresfinal=unique(scoresfinal);
% graphdurs=[];
% for k=1:length(uniquescoresfinal)
%     graphdurs(k)=mean(durationsfinal(find(scoresfinal==uniquescoresfinal(k))));
% end
% 
% figure(2)%now, for each score across all questions, average the duration again
% scatter(graphdurs,uniquescoresfinal);
% title('overall trend, combined from indiv. question trends');
% %     
% % 
% %     
% % 
% % %%%%%%%%EACH DOT ON THIS GRAPH IS A QUESTION
% %      dur=[];
% %     score=[];
% %     freq_threshold=100;%minimum number of samples in order to use
% %     for k=1:length(uniqueIDs)
% %       if length(find(IDs==uniqueIDs(k)))>=freq_threshold
% %         dur(k)=mean(duration(find(IDs==uniqueIDs(k))));
% %         score(k)=mean(scores(find(IDs==uniqueIDs(k))));
% %     
% %       else
% %           dur(k)=-5;
% %           score(k)=-5;
% %        %   uniqueIDs(k)=-5;%comment this out if not pulling scores
% %          end
% %     end
% % 
% %     dur(find(dur==-5))=[];
% %     score(find(score==-5))=[];
% %     figure(500)
% %      scatter(dur,score);
% %      title('scores vs duration for each question');
% %     hold on
% % 
% %     %%each dot on this graph is a duration
% %     dur=[];
% %     score=[];
% %  uniquedurs=unique(duration);
% %  for i=1:length(uniquedurs)
% %  uniquedurs(length(find(uniquedurs(i)==duration))<25)=-5;
% %  end
% %   uniquedurs(find(uniquedurs==-5))=[];
% %  for i=1:length(uniquedurs)
% %      score(i)=mean(scores(find(duration==uniquedurs(i))));
% %  end
% %  
% %  figure(501)
% %  scatter(uniquedurs,score)
% %  title('scores vs duration for each unique duration')
% %     

%%%%pull the difficulties%%%
% 
% difficulties=exec(dbConn,['select id, difficulty from questions where difficulty is not null;']);
% difficulties=fetch(difficulties);
% difficulties=difficulties.Data;
% difficulties=difficulties';
% index=[difficulties{:,1}];
% graphIDs=uniqueIDs(index);
% scores=
% plot([difficulties{:,2}],scores);

     %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
else
    disp(sprintf('Connection failed: %s', dbConn.Message));
end
toc
% Close the connection so we don't run out of MySQL threads
close(dbConn);
