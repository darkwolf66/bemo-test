<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Spatie\DbDumper\Databases\MySql;
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
            ->dumpToFile(Config::get('dbdumper.dump_path'));
        return response()->json('/last-dump.sql');
    }
}
