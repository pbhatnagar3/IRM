-- MySQL dump 10.11
--
-- Host: localhost    Database: its
-- ------------------------------------------------------
-- Server version	5.0.77

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `questions_difficulty`
--

DROP TABLE IF EXISTS `questions_difficulty`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `questions_difficulty` (
  `id` int(11) NOT NULL auto_increment,
  `q_id` int(11) default NULL,
  `difficulty` decimal(5,4) default NULL,
  `difficulty2` decimal(5,4) default NULL,
  `difficultySTD` decimal(5,4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=409 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `questions_difficulty`
--

LOCK TABLES `questions_difficulty` WRITE;
/*!40000 ALTER TABLE `questions_difficulty` DISABLE KEYS */;
INSERT INTO `questions_difficulty` VALUES (1,4,'5.2687','5.0608','4.3254'),(2,5,'6.5375','5.5344','5.7850'),(3,6,'6.1920','4.3510','4.2331'),(4,7,'4.9608','4.9484','4.7272'),(5,8,'5.7190','5.1009','4.8244'),(6,9,'6.2442','5.9446','5.2080'),(7,16,'4.6861','4.8822','4.0979'),(8,17,'5.2388','3.9994','3.7560'),(9,19,'4.8012','3.8303','3.6218'),(10,23,'5.2702','5.1056','4.8985'),(11,26,'5.6367',NULL,'4.7913'),(12,27,'5.1448','1.2000','3.3109'),(13,32,'5.5668','4.0302','4.4174'),(14,40,'5.1973','5.0415','4.5599'),(15,42,'5.3644',NULL,'5.0337'),(16,48,'4.9063',NULL,'3.6679'),(17,49,'5.5186',NULL,'4.9712'),(18,51,'5.8890',NULL,'5.2524'),(19,52,'5.1916','5.1940','4.0986'),(20,57,'5.4772','4.4888','3.7700'),(21,72,'8.9747','9.0000','8.2710'),(22,77,'5.0337','0.8030','3.7381'),(23,78,'5.3988','5.1795','4.3739'),(24,84,'5.4887',NULL,'5.0457'),(25,88,'7.9968','5.7609','7.3795'),(26,93,'5.2444','5.8560','4.2579'),(27,95,'6.8375','6.5867','6.4309'),(28,103,'8.3260','6.4652','6.8417'),(29,112,'6.8209','4.0606','5.2689'),(30,114,'4.8627','3.5551','2.9194'),(31,120,'6.0303',NULL,'5.0746'),(32,122,'5.2007','5.2554','4.9199'),(33,124,'6.0046',NULL,'4.8490'),(34,127,'4.6513','4.5081','3.9452'),(35,133,'8.9832','8.9615','8.7272'),(36,142,'5.3702',NULL,'4.5752'),(37,145,'6.1416','4.7443','4.4810'),(38,148,'5.0828',NULL,'3.2706'),(39,155,'4.9840','2.7175','3.2120'),(40,156,'6.9875','6.6728','6.1698'),(41,164,'6.2086',NULL,'4.6577'),(42,174,'8.5747','5.8616','7.4213'),(43,211,'6.3701','5.1620','4.0522'),(44,220,'6.7362','4.7427','5.2404'),(45,222,'4.3147','3.6457','2.6190'),(46,226,'5.0000','5.0000','4.7272'),(47,238,'8.9495','8.9618','8.7272'),(48,242,'8.8029','8.7283','8.2710'),(49,244,'7.0909','4.4764','5.0584'),(50,247,'5.3459',NULL,'4.6896'),(51,248,'5.1220','2.8007','4.1198'),(52,250,'4.9439','4.9263','4.7272'),(53,255,'5.2109','3.5956','2.3972'),(54,256,'5.7344','6.0518','5.1730'),(55,272,'5.0538','5.2167','2.9654'),(56,276,'5.6660','5.7654','3.4972'),(57,278,'6.3543','2.8066','5.4770'),(58,283,'5.5036',NULL,'3.9572'),(59,288,'5.1081','3.8369','2.9617'),(60,297,'5.3857','3.7789','4.0339'),(61,311,'4.5597',NULL,'3.2229'),(62,316,'5.7098','5.2809','5.0906'),(63,324,'4.9327','4.8491','4.7272'),(64,332,'4.9919',NULL,'4.3479'),(65,334,'5.1439','5.0742','4.9272'),(66,335,'6.0094','1.6158','4.8905'),(67,339,'6.1488','4.4736','4.7290'),(68,344,'5.1021','5.0196','4.5699'),(69,346,'5.3550','3.7522','3.5233'),(70,361,'5.3538',NULL,'4.8183'),(71,364,'5.4929','5.1461','4.8134'),(72,367,'6.0919','3.4243','3.4243'),(73,372,'5.1297','4.4540','4.2629'),(74,378,'8.9215',NULL,'8.7272'),(75,385,'5.2137','1.4076','3.3830'),(76,390,'6.5607','5.8964','5.4177'),(77,401,'4.7729','4.1866','3.4124'),(78,404,'5.3161','5.0040','4.4521'),(79,410,'5.9286','5.3333','4.9328'),(80,411,'5.5948','5.3493','4.9159'),(81,414,'6.6826','5.8091','4.6793'),(82,423,'6.7183','2.8937','4.7758'),(83,424,'5.5148',NULL,'3.7228'),(84,428,'5.5488','4.2271','3.4976'),(85,437,'4.5608','2.6329','2.0338'),(86,439,'4.8936','3.0983','3.3375'),(87,445,'5.1354','4.9462','4.8335'),(88,448,'5.8660','2.7935','4.3475'),(89,450,'5.1154',NULL,'3.5273'),(90,467,'7.0832','4.6432','6.1241'),(91,475,'4.5000',NULL,'4.4951'),(92,477,'5.3351','3.8766','4.6893'),(93,484,'5.5983','3.5331','3.8114'),(94,485,'4.9552','4.8994','4.7272'),(95,497,'4.7477','4.4225','4.2973'),(96,505,'5.4230','2.9804',NULL),(97,512,'4.0789','3.5963','3.1222'),(98,516,'5.7219','5.4450','5.3470'),(99,530,'6.3389','5.3846','4.7705'),(100,540,'5.0000','5.0000','4.7272'),(101,542,'5.5049','3.2039','4.7225'),(102,554,'6.2537','4.6509','4.3703'),(103,556,'8.9944','9.0000','8.7272'),(104,557,'8.9141',NULL,'8.2710'),(105,567,'5.6233',NULL,'3.8878'),(106,570,'8.6917','8.5497','8.1160'),(107,574,'6.5639','5.1344','4.9154'),(108,577,'4.7421','4.6612','4.2792'),(109,582,'5.5755','1.9801','5.2415'),(110,587,'6.1559','4.0532','4.1653'),(111,600,'4.5403',NULL,'3.6266'),(112,618,'5.0562',NULL,'4.3876'),(113,644,'6.2267',NULL,'5.1106'),(114,651,'5.9333','6.5075','4.8693'),(115,653,'8.3995','4.2544','7.7543'),(116,658,'6.1037','5.8475','4.8771'),(117,661,'6.4792','4.4563','5.5708'),(118,672,'6.1162','2.1224','4.1222'),(119,676,'5.4569','2.9018',NULL),(120,677,'5.3739','0.6449','4.6599'),(121,680,'7.1051','6.2054','6.2807'),(122,690,'7.6823','7.8000','7.5272'),(123,692,'7.9664','7.9674','7.7272'),(124,696,'5.2309','5.7904','4.1370'),(125,705,'8.0832','6.0561','6.2792'),(126,715,'7.9703','3.7064','6.6429'),(127,722,'4.9838','3.5149','3.3388'),(128,725,'4.6861','3.3375','4.0979'),(129,726,'5.1555','2.8501','2.3511'),(130,731,'8.9899','8.8323','8.2710'),(131,735,'9.0000','9.0000','8.2710'),(132,740,'6.2676','5.5937','5.0694'),(133,756,'5.4476','4.8282','4.6844'),(134,759,'5.3617','3.6310','4.0340'),(135,764,'8.9747','8.5807','8.2710'),(136,774,'4.8823','4.0495','4.7272'),(137,775,'5.3555','3.0642',NULL),(138,785,'5.7961','4.6011','5.0458'),(139,790,'5.8319','5.3142','4.7658'),(140,791,'3.5032','2.5000','2.2272'),(141,804,'8.9899','9.0000','8.2710'),(142,814,'5.0746','3.1423','3.3380'),(143,815,'5.2417','3.3822','4.7477'),(144,816,'5.3767','3.4236','4.7782'),(145,817,'5.5053',NULL,'4.8403'),(146,828,'6.0022','2.0404','5.0487'),(147,835,'7.6767','7.7856','7.5143'),(148,836,'8.9495','9.0000','8.7272'),(149,845,'5.1764','1.7597','2.3119'),(150,853,'5.0821','2.3328','4.1915'),(151,855,'5.7990','3.6603','3.8943'),(152,857,'8.8542','9.0000','8.6418'),(153,863,'5.0568','3.9294','2.8963'),(154,866,'6.2053','5.6307','5.3595'),(155,868,'4.6256','3.9798','3.6168'),(156,874,'4.9832','4.8642','4.7272'),(157,879,'5.0339','4.9650','2.6538'),(158,885,'6.3098','4.1483','3.4191'),(159,886,'7.5136','7.2110','6.6662'),(160,888,'6.3368','5.5669','5.0082'),(161,891,'5.0589','4.2033','2.9721'),(162,893,'5.4568','3.1269','3.8522'),(163,898,'6.1258','5.9720','5.8114'),(164,900,'5.4102',NULL,'4.2191'),(165,909,'8.9439','9.0000','8.7272'),(166,911,'4.9720','5.0000','4.7272'),(167,917,'5.8468','4.2918','5.1864'),(168,919,'5.3580','2.5696','2.9077'),(169,931,'6.0586','6.2708','5.3315'),(170,943,'6.5714','2.6960','5.4603'),(171,945,'3.9517','2.5000','2.2272'),(172,946,'6.2632',NULL,'4.7484'),(173,959,'8.9495','8.9337','8.7272'),(174,964,'6.0646','5.8140','5.1646'),(175,971,'5.5504','4.5608','3.9311'),(176,982,'6.9141','6.6251','5.3604'),(177,986,'8.8374','8.7368','8.5874'),(178,990,'5.6579','4.7944','4.1691'),(179,1000,'5.9061','4.9298','4.2871'),(180,1004,'4.8374','4.6353','4.5874'),(181,1007,'5.3458','4.3218','4.6472'),(182,1010,'5.1966','5.5399','3.3378'),(183,1012,'5.8725','3.7824','4.1672'),(184,1013,'4.9523','4.4552','3.7875'),(185,1017,'6.1637',NULL,'4.8652'),(186,1018,'4.7623','3.0350','3.3099'),(187,1019,'5.4735',NULL,'4.8105'),(188,1021,'6.2555',NULL,'4.8532'),(189,1022,'5.1549',NULL,'3.3070'),(190,1023,'4.9893','0.5869','3.9422'),(191,1025,'4.9954','3.1607','3.7745'),(192,1026,'5.4091','3.2137','4.4310'),(193,1027,'4.5942','3.2247','2.4028'),(194,1029,'5.6071','5.5036','5.1526'),(195,1031,'6.0791','4.9474','4.3395'),(196,1033,'8.2376','6.5000','6.6476'),(197,1034,'4.9720',NULL,'4.7272'),(198,1039,'5.9965',NULL,'4.7277'),(199,1041,'5.2804','6.0668','4.0354'),(200,1043,'5.3129','5.9883','4.1093'),(201,1045,'5.3868','3.4408','2.4995'),(202,1052,'5.7375','3.4769','3.7778'),(203,1056,'8.9776','9.0000','8.7272'),(204,1058,'6.5239','7.2989','5.1925'),(205,1060,'5.8463',NULL,'5.3167'),(206,1063,'5.1224','5.3407','4.3125'),(207,1068,'6.2424','3.1123',NULL),(208,1069,'5.1279','4.8383','4.7459'),(209,1070,'4.6332','3.0462','1.9898'),(210,1072,'5.9305','3.9440','5.0079'),(211,1073,'6.7479','6.4471','5.9064'),(212,1075,'4.7301','3.1407','3.1980'),(213,1076,'5.0311',NULL,'3.8562'),(214,1081,'5.6056','3.0472','4.7488'),(215,1082,'4.5384','2.5455','1.5693'),(216,1083,'4.9215','4.8239','4.7272'),(217,1084,'8.9899','8.8323','8.2710'),(218,1085,'6.0640','5.2376','4.1440'),(219,1086,'5.1471',NULL,'3.8590'),(220,1089,'6.3638',NULL,'5.0710'),(221,1091,'2.9391','2.6332','2.3941'),(222,1096,'6.5331',NULL,'5.1356'),(223,1098,'6.5837',NULL,'5.0888'),(224,1099,'6.0420',NULL,'3.6205'),(225,1100,'5.2487',NULL,'4.0753'),(226,1190,'5.4049','3.9327','3.0773'),(227,1191,'4.2486','2.8255','1.9420'),(228,1193,'4.8473',NULL,'2.7698'),(229,1194,'4.9793','3.5965','3.3616'),(230,1196,'5.5833',NULL,'4.8814'),(231,1197,'5.2427','5.3039','4.7516'),(232,1199,'5.6657','5.1219','3.7599'),(233,1200,'7.1718','7.2887','6.7495'),(234,1201,'6.4624','6.2767','4.7425'),(235,1202,'6.7293','3.6131','4.7396'),(236,1203,'5.2816',NULL,'4.7622'),(237,1206,'5.7408',NULL,'3.8973'),(238,1207,'3.8305','3.2090','3.0385'),(239,1208,'5.4307','4.9901','4.8326'),(240,1209,'5.5602','5.4165','5.1243'),(241,1210,'4.9664',NULL,'4.7272'),(242,1211,'5.7262',NULL,'4.8483'),(243,1212,'6.7910','5.2561','5.7643'),(244,1213,'5.6372',NULL,'5.5424'),(245,1214,'3.7939',NULL,'2.9164'),(246,1215,'4.5451','4.9704','2.8595'),(247,1216,'5.0726','3.1404','3.6289'),(248,1217,'5.1305','5.8024','4.1041'),(249,1218,'5.3312','4.0850','2.7753'),(250,1219,'5.9909',NULL,'3.8607'),(251,1220,'5.0605','3.6210','4.0619'),(252,1221,'5.4026','4.2017','3.8580'),(253,1222,'4.6419','2.6959','2.3983'),(254,2119,'7.3369','6.1440','6.6048'),(255,2124,'5.8254','3.0688','3.8602'),(256,2125,'5.6611','5.7580','5.3380'),(257,2126,'5.7988','6.5391','4.2782'),(258,2127,'5.8151','2.9376','3.9906'),(259,2130,'7.4083','7.4533','5.7261'),(260,2131,'4.6973','4.8721','4.1342'),(261,2132,'6.0176','6.1085','5.3063'),(262,2133,'5.4618','4.4393','3.9450'),(263,2137,'6.4830',NULL,'3.8844'),(264,2138,'5.9256',NULL,'4.1379'),(265,2139,'5.5544',NULL,'3.7612'),(266,2140,'5.0476',NULL,'3.2073'),(267,2145,'8.5683',NULL,'7.7172'),(268,2155,'5.5036',NULL,'3.4161'),(269,2156,'6.6180',NULL,'4.6505'),(270,2157,'6.3610',NULL,'4.7372'),(271,2158,'5.0880',NULL,'3.5513'),(272,2159,'5.3565',NULL,'3.3949'),(273,2160,'5.1444',NULL,'2.3247'),(274,2161,'5.1187',NULL,'4.2206'),(275,2163,'5.5358',NULL,'3.8452'),(276,2164,'5.0919',NULL,'2.4871'),(277,2181,'5.0653',NULL,'4.4862'),(278,2182,'5.5539',NULL,'5.0479'),(279,2183,'7.0309',NULL,'6.2514'),(280,2184,'6.2003',NULL,'5.6164'),(281,2185,'7.7269',NULL,'6.9285'),(282,2186,'6.6050',NULL,'5.9911'),(283,2187,'4.9944',NULL,'4.7272'),(284,2188,'5.6334',NULL,'3.9019'),(285,2189,'6.2571',NULL,'4.5221'),(286,2190,'5.7249',NULL,'3.5485'),(287,2191,'6.0586',NULL,'4.5903'),(288,2192,'5.7055',NULL,'4.1091'),(289,2193,'5.4701',NULL,'4.0220'),(290,2194,'6.7033',NULL,'5.1780'),(291,2195,'6.0451',NULL,'4.4687'),(292,2196,'5.4029',NULL,'4.0676'),(293,2197,'7.9983',NULL,'6.7198'),(294,2198,'6.4859',NULL,'5.6098'),(295,2199,'5.9526',NULL,'5.1367'),(296,2200,'5.5037',NULL,'3.9772'),(297,2201,'7.3328',NULL,'6.1925'),(298,2203,'5.3454',NULL,'4.9657'),(299,2204,'5.8434',NULL,'4.7583'),(300,2205,'5.4440',NULL,'4.4839'),(301,2206,'5.0992',NULL,'4.3376'),(302,2207,'5.1086',NULL,'4.5591'),(303,2208,'5.4577',NULL,'4.5033'),(304,2209,'5.1274',NULL,'4.5610'),(305,2210,'6.2688',NULL,'4.3076'),(306,2211,'5.6749',NULL,'4.4621'),(307,2212,'6.0253',NULL,'3.4277'),(308,2214,'6.2319',NULL,'5.4919'),(309,2215,'6.4665',NULL,'5.4763'),(310,2216,'5.4547',NULL,'3.7638'),(311,2217,'6.7807',NULL,'5.3191'),(312,2218,'5.3551',NULL,'4.1645'),(313,2219,'4.6672',NULL,'4.0542'),(314,2220,'5.4272',NULL,'3.4167'),(315,2221,'5.1326',NULL,'3.7271'),(316,2222,'4.8038',NULL,'3.7265'),(317,2223,'4.4694',NULL,'3.8096'),(318,2224,'5.1515',NULL,'4.1435'),(319,2225,'4.7915',NULL,'2.8588'),(320,2226,'5.1415',NULL,'4.0183'),(321,2227,'4.3811',NULL,'3.0883'),(322,2228,'5.1989',NULL,'2.8209'),(323,2229,'5.6765',NULL,'4.1907'),(324,2234,'4.8796',NULL,'3.5121'),(325,2235,'5.2868',NULL,'3.4890'),(326,2236,'5.0602',NULL,'3.4094'),(327,2237,'6.2121',NULL,'3.8915'),(328,2238,'6.7575',NULL,'4.8252'),(329,2239,'6.6229',NULL,'6.1709'),(330,2240,'7.0431',NULL,'5.5852'),(331,2241,'5.8940',NULL,'4.2736'),(332,2242,'5.1264',NULL,'4.2964'),(333,2243,'6.3285',NULL,'4.4786'),(334,2244,'5.3674',NULL,'3.0349'),(335,2245,'5.5582',NULL,'3.8531'),(336,2246,'5.1614',NULL,'2.4795'),(337,2247,'5.2169',NULL,'2.6499'),(338,2248,'5.2574',NULL,'3.1975'),(339,2249,'6.0548',NULL,'4.7118'),(340,2250,'5.4919',NULL,'3.6719'),(341,2251,'5.3250',NULL,'3.9689'),(342,2252,'6.0108',NULL,'3.9278'),(343,2253,'5.2899',NULL,'2.4462'),(344,2254,'5.1892',NULL,'2.6447'),(345,2257,'4.5780',NULL,'3.8582'),(346,2258,'4.7306',NULL,'4.2316'),(347,2259,'6.2903',NULL,'4.4772'),(348,2260,'5.2600',NULL,'3.0956'),(349,2261,'5.8701',NULL,'4.0824'),(350,3161,'6.7834',NULL,'5.8695'),(351,3162,'6.0740',NULL,'4.4024'),(352,3163,'6.8720',NULL,'6.0776'),(353,3164,'5.4208',NULL,'4.1843'),(354,3165,'4.9290',NULL,'3.1471'),(355,3166,'5.3300',NULL,'4.7848'),(356,3167,'5.2991',NULL,'4.0023'),(357,3168,'5.6260',NULL,'4.4762'),(358,3169,'5.8335',NULL,'4.7511'),(359,3170,'5.4741',NULL,'3.9087'),(360,3171,'5.8295',NULL,'3.9578'),(361,3172,'5.9656',NULL,'3.8855'),(362,3173,'6.7305',NULL,'5.6496'),(363,3174,'4.9098',NULL,'3.1114'),(364,3175,'6.2514',NULL,'4.9436'),(365,3176,'6.4712',NULL,'4.6855'),(366,3177,'6.2209',NULL,'4.0526'),(367,3178,'7.6627',NULL,'5.7984'),(368,3179,'7.1742',NULL,'5.1124'),(369,3180,'6.5212',NULL,'4.2824'),(370,3226,'4.9600',NULL,'3.3134'),(371,3228,'5.4936',NULL,'3.2044'),(372,3302,'5.6398',NULL,'4.8505'),(373,3303,'5.5298',NULL,'3.9947'),(374,3304,'7.0071',NULL,'5.0019'),(375,3305,'7.2976',NULL,'5.1882'),(376,3306,'7.0847',NULL,'5.0754'),(377,3307,'5.7215',NULL,'3.7163'),(378,3308,'7.5895',NULL,'5.6651'),(379,3309,'8.5293',NULL,'6.8998'),(380,3310,'6.2529',NULL,'4.7856'),(381,3311,'7.0203',NULL,'5.0140'),(382,3312,'8.3132',NULL,'7.2527'),(383,3313,'7.7307',NULL,'6.0829'),(384,3314,'5.3586',NULL,'4.6722'),(385,3315,'7.0486',NULL,'5.1446'),(386,3316,'6.4884',NULL,'5.6424'),(387,3317,'8.0412',NULL,'7.0964'),(388,3318,'6.6959',NULL,'4.3256'),(389,3319,'4.9599',NULL,'2.4898'),(390,3320,'5.1065',NULL,'3.0867'),(391,3321,'6.1252',NULL,'4.2705'),(392,3322,'6.7727',NULL,'4.9430'),(393,3323,'6.4965',NULL,'4.9382'),(394,3324,'8.1837',NULL,'6.5606'),(395,3325,'6.7330',NULL,'5.8310'),(396,3328,'7.4984',NULL,'5.8730'),(397,3329,'7.5371',NULL,'5.4796'),(398,3330,'8.2298',NULL,'6.1421'),(399,3331,'8.2066',NULL,'6.2361'),(400,3332,'5.2721',NULL,'3.6061'),(401,3333,'8.6745',NULL,'7.2653'),(402,3336,'5.8996',NULL,'4.0277'),(403,3464,'6.5749',NULL,'5.0895'),(404,3465,'7.2234',NULL,NULL),(405,3466,'5.8292',NULL,NULL),(406,3467,'6.0973',NULL,'5.4587'),(407,3468,'5.7311',NULL,'4.2283'),(408,3469,'5.5515',NULL,'4.1106');
/*!40000 ALTER TABLE `questions_difficulty` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-10-30 20:43:37
