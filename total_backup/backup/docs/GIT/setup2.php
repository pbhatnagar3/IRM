<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>VM - UBUNTU</title>
	<link rel="stylesheet" href="../css/docs.css">
</head>
<body>
<h3>
Instructions for installing GIT on Ubuntu:
</h3><center>
  <table class="ITS_version" summary="ITS versions">
	    <!--------------------------------------------------------------------->
			<tr><th>ITEM</th><th>ACTION</th></tr>
	    <!--------------------------------------------------------------------->
	    <tr>
		  <td>Installing GIT</td>
			<td class="list">
			  <ol>
				<li><code>sudo apt-get install libcurl4-gnutls-dev libexpat1-dev gettext libz-dev libssl-dev</code></li>
				<li><code>sudo apt-get install git-core git-gui gitolite</code></li>				
        			</ol>

			</td>
</tr>
<tr>
		<td>Configuring GIT</td>
			<td class="list">
				<ol>
				<li><code> git config --global user.name "&lt;your_name&gt;"</code></li>
				<li><code> git config --global user.email &lt;your_email&gt;</code></li>
				<li><code> git config --global core.editor emacs</code></li>
				<li><code> git config --global merge.tool vimdiff</li></code>
				</ol>
			</td>
		  </tr>	
<tr>
		<td>Setting Up Repository on Server</td>
			<td class="list">
				<ol>
				<li><code>ssh -X &lt;username&gt;@itsdev5.vip.gatech.edu</li></code>
				<li><code>cd /home/&lt;username&gt;</li></code>
				<li><code>git clone &lt;username&gt;@itsdev5.vip.gatech.edu:/opt/git/ITS-GT.git html</li></code>
				<li><code>cd html</li></code>
				<li><code>git branch dev</li></code>
				</ol>
			</td>
</tr>
<tr>
		<td>Setting Up Repository on Local</td>
			<td class="list">
				<ol>
				<li><code>cd /var/www</li></code>
				<li><code>git clone &lt;username&gt;@itsdev5.vip.gatech.edu:/home/&lt;username&gt;/html</li></code>
				<li><code>cd html</li></code>
				</ol>
			</td>

</tr>
<tr>
		<td>Pushing to Remote Repository</td>
			<td class="list">
				<ol>
				<li><code>git add *</li></code>
				<li><code>git commit -am "first message"</li></code>
				<li><code>git push origin --delete dev</li></code>
				<li><code>git push origin HEAD:dev</li></code>
				</ol>
			</td>

</tr>
<tr>
		<td>[On Server] Merging dev with master Branch</td>
			<td class="list">
				<ol>
				<li><code>cd /home/&lt;username&gt;/html</li></code>
				<li><code>git checkout master</li></code>
				<li><code>git merge dev</li></code>
			</td>

</tr>
	    <!--------------------------------------------------------------------->			
  </table></center>
</body>
</html>
