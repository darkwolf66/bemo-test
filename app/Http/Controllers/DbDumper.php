<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mockery\Exception;
use Spatie\DbDumper\Databases\MySql;
use Ramsey\Uuid\Nonstandard\Uuid;

class DbDumper extends Controller
{
    /**
     * Used to dump the db
     * @return \Illuminate\Http\JsonResponse
     * @throws \Spatie\DbDumper\Exceptions\CannotStartDump
     * @throws \Spatie\DbDumper\Exceptions\DumpFailed
     */
    public function downloadDb(){
        MySql::create()
            ->setDbName(Config::get('database.connections.mysql.database'))
            ->setUserName(Config::get('database.connections.mysql.username'))
            ->setPassword(Config::get('database.connections.mysql.password'))
            ->setDumpBinaryPath('storage/app/public/')
            ->dumpToFile('/last-dump.sql');
        return response()->json('/last-dump.sql');
    }
}
