<?php

namespace Codenexus\GeoIPlm;

use GeoIp2\Database\Reader;

class GeoIPUpdater
{
	/**
	 * Main update method
	 *
	 * @return bool|string
	 */
	public function update()
	{
		$url = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';
		$databasePath = storage_path('app/geoip.mmdb');

		// Download latest MaxMind GeoLite2 City database to temp location
		$tmpFile = tempnam(sys_get_temp_dir(), 'maxmind');
		file_put_contents($tmpFile . 'gz', fopen($url, 'r'));

        // Extract database
		file_put_contents($tmpFile, gzopen($tmpFile . 'gz', 'r'));

        if (!$this->_testDb($tmpFile))
        {
            return false;
        }

        rename($tmpFile, $databasePath);

		return $databasePath;
	}

    protected function _testDb($path)
    {
        $reader = new Reader($path);
		$record = $reader->city('8.8.8.8');

        if ($record->city->name === 'Mountain View')
        {
            return true;
        }

        return false;
    }
}
