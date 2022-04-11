<?php
/* Get pms version and lite version. */
$pmsVersion = $argv[1];
if(empty($pmsVersion)) die("Please give me pms version.\n");
$liteVersion = isset($argv[2]) ? $argv[2] : '';

/* File pathes. */
$basePath    = dirname(dirname(__FILE__));
$releasePath = getenv('ZENTAO_RELEASE_PATH');
$releasePath = !empty($releasePath) ? $releasePath : $basePath;
if(!file_exists($releasePath . '/zentaopms.zip')) die("Please give me encrypted packages.\n");

/* Get the encrypted packages. */
`cp $releasePath/zentaopms.zip $basePath`;
`unzip zentaopms.zip; rm zentaopms.zip`;

/* Encrypted packages list. */
$fileList   = array();
$fileList[] = "{$basePath}/zentaobiz.php5.3_5.6.zip";
$fileList[] = "{$basePath}/zentaobiz.php7.0.zip";
$fileList[] = "{$basePath}/zentaobiz.php7.1.zip";
$fileList[] = "{$basePath}/zentaobiz.php7.2_7.4.zip";

$fileList[] = "{$basePath}/zentaomax.php5.3_5.6.zip";
$fileList[] = "{$basePath}/zentaomax.php7.0.zip";
$fileList[] = "{$basePath}/zentaomax.php7.1.zip";
$fileList[] = "{$basePath}/zentaomax.php7.2_7.4.zip";

foreach($fileList as $file) if(!file_exists($file)) echo basename($file) . " is not exists\n";

/* Create shells to make zip format packages. */
$shellList = array();
foreach(array('zh-cn', 'en') as $langType)
{
    /* Init vars. */
    $packPrefix = $langType == 'zh-cn' ? 'ZenTaoPMS' : 'ZenTaoALM';
    $version    = $langType == 'zh-cn' ? $pmsVersion : $pmsVersion . '.int';
    $dirName    = $langType == 'zh-cn' ? 'zentaopms' : 'zentaoalm';
    if(!empty($liteVersion)) $liteVersionAB = $langType == 'zh-cn' ? $liteVersion : $liteVersion . '.int';

    /* Cycle the php versions. */
    foreach(array('5.3_5.6', '7.0', '7.1', '7.2_7.4', '8.0') as $phpVersion)
    {
        /* File name. */
        $workDir   = "tmp/$packPrefix.{$version}.{$phpVersion}/";
        $shellName = $workDir . "make.sh";
        echo "Creating $shellName\n";

        mkdir($workDir, 0777, true);

        /* The commands of the shell. */
        $command  = "cd $workDir\n";
        $command .= "if [ ! -d ../$dirName ]; then unzip ../../$packPrefix.{$version}.zip -d ../ ; fi\n";
        $command .= "cp -raf ../$dirName .\n";
        $command .= "unzip ../../zentaobiz.php{$phpVersion}.zip\n";
        $command .= "unzip ../../zentaomax.php{$phpVersion}.zip\n";
        $command .= "cp -rf biz/* $dirName/\n";
        $command .= "cp -rf max/* $dirName/\n";
        $command .= "zip -r ../../$packPrefix.{$version}.php{$phpVersion}.zip $dirName\n";

        if(!empty($liteVersion))
        {
            $command .= "cd $dirName/config/ext; touch visions.php; echo '<?php\n\$config->visions = \",lite,\";' > visions.php\n";
            $command .= "echo $liteVersionAB > $dirName/VERSION\n";
            $command .= "zip -r ../../$packPrefix.{$liteVersionAB}.php{$phpVersion}.zip $dirName\n";
        }

        $command .= "rm -rf $dirName/ biz/ max/\n";

        file_put_contents($shellName, $command);

        $shellList[] = $shellName;
    }
}

/* Execute the shells. */
$lines = '';
foreach($shellList as $shellName)
{
    echo $shellName . "\n";
    $lines .= "sh $shellName &\n";
}
$lines .= "wait\necho 'Zip packages has done.'";
file_put_contents('zip.sh', $lines);
