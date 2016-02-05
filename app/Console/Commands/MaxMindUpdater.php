
<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class MaxMingUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maxmind:update';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates MaxMind Geolocation DB';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit ( 0 );

        $this->copyfile_chunked('http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',database_path());

    }


    /**
     * Thanks to this guy on stackoverflow! http://stackoverflow.com/a/4000569
     *
     * Copy remote file over HTTP one small chunk at a time.
     *
     * @param $infile string The full URL to the remote file
     * @param $outfile string The path where to save the file
     */
    function copyfile_chunked($infile, $outfile) {
        $chunksize = 10 * (1024 * 1024); // 10 Megs

        print "Downloading MaxMind update.... \r\n";

        /**
         * parse_url breaks a part a URL into it's parts, i.e. host, path,
         * query string, etc.
         */
        $parts = parse_url($infile);
        $i_handle = fsockopen($parts['host'], 80, $errstr, $errcode, 5);
        $o_handle = fopen($outfile, 'wb');

        if ($i_handle == false || $o_handle == false) {
            return false;
        }

        if (!empty($parts['query'])) {
            $parts['path'] .= '?' . $parts['query'];
        }

        /**
         * Send the request to the server for the file
         */
        $request = "GET {$parts['path']} HTTP/1.1\r\n";
        $request .= "Host: {$parts['host']}\r\n";
        $request .= "User-Agent: Mozilla/5.0\r\n";
        $request .= "Keep-Alive: 115\r\n";
        $request .= "Connection: keep-alive\r\n\r\n";
        fwrite($i_handle, $request);

        /**
         * Now read the headers from the remote server. We'll need
         * to get the content length.
         */
        $headers = array();
        while(!feof($i_handle)) {
            $line = fgets($i_handle);
            if ($line == "\r\n") break;
            $headers[] = $line;
        }

        /**
         * Look for the Content-Length header, and get the size
         * of the remote file.
         */
        $length = 0;
        foreach($headers as $header) {
            if (stripos($header, 'Content-Length:') === 0) {
                $length = (int)str_replace('Content-Length: ', '', $header);
                break;
            }
        }

        $bar = $this->output->createProgressBar(count($length));

        /**
         * Start reading in the remote file, and writing it to the
         * local file one chunk at a time.
         */
        $cnt = 0;
        while(!feof($i_handle)) {
            $buf = '';
            $buf = fread($i_handle, $chunksize);
            $bytes = fwrite($o_handle, $buf);
            if ($bytes == false) {
                return false;
            }
            $cnt += $bytes;
            $bar->advance();
            /**
             * We're done reading when we've reached the conent length
             */
            if ($cnt >= $length) break;
        }

        $bar->finish();

        fclose($i_handle);
        fclose($o_handle);
        return $cnt;
    }
}