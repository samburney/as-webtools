<?
        $site = "Ridgehaven";

        apache_setenv('no-gzip', 1);
        ini_set('zlib.output_compression', 0);
        ini_set('output_buffering', 0);
?>
<html>
<head>
<title>Traceroute from <?= $site ?></title>
</head>
<body>
<h2>Traceroute from <?= $site ?></h2>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
        <p>
                <b>Host</b>
                <input type="text" name="host" value="<?= $_REQUEST['host'] ?>" />
        </p>
        <p>
                <input type="submit" />
        </p>
</form>
<? for( $i = 0; $i < 1024; $i++ ) { print " "; } ?>
<? if($_REQUEST['host']){ ?>
<pre>
<?
        if( !preg_match("/^[\w\d\.-_]+/", $_REQUEST['host'] ) )
        {
                print "Please enter a valid hostname";
                exit;
        }

        $cmd = escapeshellcmd( "/usr/sbin/traceroute \"". $_REQUEST['host'] ."\"" );

        $fp = popen( $cmd ." 2>&1", "r" );

        if( !$fp ) { print "An error occured running $cmd"; exit; }

        flush();

        while ( !feof($fp) )
        {
                print fgets( $fp );
                ob_flush(); flush();
        }

        pclose( $fp );
?>
</pre>
<? } ?>
</body>
</html>
