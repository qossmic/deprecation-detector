<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class FunctionViolationChecker implements ViolationCheckerInterface
{
    private $deprecatedFunctions;

    private static $deprecatedPhpFunctions = array(
        'call_user_method' => 'Since PHP 5.3, use call_user_func() instead',
        'call_user_method_array' => 'call_user_func_array',
        'define_syslog_variables' => 'Since PHP 5.3',
        'dl' => 'Since PHP 5.3',
        'ereg' => 'Since PHP 5.3, use  preg_match() instead',
        'ereg_replace' => 'Since PHP 5.3, use  preg_replace() instead',
        'eregi' => 'Since PHP 5.3, use preg_match() with the "i" modifier instead',
        'eregi_replace' => 'Since PHP 5.3, use preg_match() with the "i" modifier instead',
        'set_magic_quotes_runtime' => 'Since PHP 5.3',
        'magic_quotes_runtime' => 'Since PHP 5.3',
        'session_register' => 'Since PHP 5.3, use the $_SESSION superglobal instead',
        'session_unregister' => 'Since PHP 5.3, use the $_SESSION superglobal instead',
        'session_is_registered' => 'Since PHP 5.3, use the $_SESSION superglobal instead',
        'set_socket_blocking' => 'Since PHP 5.3, use stream_set_blocking() instead',
        'split' => 'Since PHP 5.3, use preg_split() instead',
        'spliti' => 'Since PHP 5.3, use preg_split() with the "i" modifier instead)',
        'sql_regcase' => 'Since PHP 5.3',
        'mysql_db_query' => 'Since PHP 5.3, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_escape_string' => 'Since PHP 4.3, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_list_dbs' => 'Since PHP 5.3, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'get_magic_quotes_gpc' => 'Since PHP 5.4',
        'get_magic_quotes_runtime' => 'Since PHP 5.4',
        'mcrypt_generic_end' => 'Since PHP 5.4',
        'mcrypt_cbc' => 'Since PHP 5.5',
        'mcrypt_cfb' => 'Since PHP 5.5',
        'mcrypt_ecb' => 'Since PHP 5.5',
        'mcrypt_ofb' => 'Since PHP 5.5',
        'mysql_affected_rows' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_client_encoding' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_close' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_connect' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_create_db' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_data_seek' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_db_name' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_drop_db' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_errno' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_error' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_fetch_array' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_fetch_assoc' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_fetch_field' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_fetch_lengths' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_fetch_object' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_fetch_row' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_field_flags' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_field_len' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_field_name' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_field_seek' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_field_table' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_field_type' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_free_result' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_get_client_info' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_get_host_info' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_get_proto_info' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_get_server_info' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_info' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_insert_id' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_list_fields' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_list_processes' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_list_tables' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_num_fields' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_num_rows' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_pconnect' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_ping' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_query' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_real_escape_string' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_result' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_select_db' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_set_charset' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_stat' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_tablename' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_thread_id' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
        'mysql_unbuffered_query' => 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.',
    );

    public function __construct(array $deprecatedFunctions = array(), $phpDeprecations = true)
    {
        if ($phpDeprecations === true) {
            $this->deprecatedFunctions = array_merge($deprecatedFunctions, static::$deprecatedPhpFunctions);
        } else {
            $this->deprecatedFunctions = $deprecatedFunctions;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet)
    {
        $violations = array();

        foreach ($phpFileInfo->getFunctionUsages() as $functionUsage) {
            if (array_key_exists($functionUsage->name(), $this->deprecatedFunctions)) {
                $violations[] = new Violation(
                    $functionUsage,
                    $phpFileInfo,
                    $this->deprecatedFunctions[$functionUsage->name()]
                );
            }
        }

        return $violations;
    }
}
