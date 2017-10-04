--TEST--
Exception is thrown if the unsupported attribute ATTR_PERSISTENT is put into the connection options
--SKIPIF--
<?php require('skipif_mid-refactor.inc'); ?>
--FILE--
<?php
require_once("MsSetup.inc");
try {
    echo "Testing a connection with ATTR_PERSISTENT...\n";
    // setting PDO::ATTR_PERSISTENT in PDO constructor returns an exception
    $dsn = "sqlsrv:Server = $server;database = $databaseName;";
    if ($keystore != "none")
        $dsn .= "ColumnEncryption=Enabled;";
    if ($keystore == "ksp") {   
        require_once('AE_Ksp.inc');
        $ksp_path = getKSPPath();
        $dsn .= "CEKeystoreProvider=$ksp_path;CEKeystoreName=$ksp_name;CEKeystoreEncryptKey=$encrypt_key;";
    }
    $attr = array(PDO::ATTR_PERSISTENT => true); 
    $conn = new PDO($dsn, $uid, $pwd, $attr); 
    
    //free the connection 
    unset($conn);
} catch(PDOException $e) {
    echo "Exception from unsupported attribute (ATTR_PERSISTENT) is caught\n";
}
try {
    require_once("MsCommon_mid-refactor.inc");
    echo "\nTesting new connection after exception thrown in previous connection...\n";
    $tableName1 = getTableName('tab1');
    $conn = connect();
    createTable($conn, $tableName1, array("c1" => "int", "c2" => "varchar(10)"));
    insertRow($conn, $tableName1, array("c1" => 1, "c2" => "column2"), "exec");
    
    $result = selectRow($conn, $tableName1, "PDO::FETCH_ASSOC");
    if ($result['c1'] == 1 && $result['c2'] == 'column2') {
        echo "Test successfully completed";
    }
    //free the statement and connection 
    dropTable($conn, $tableName);
    unset($stmt);
    unset($conn);
} catch(PDOException $e) {
    var_dump( $e);
}
?> 
--EXPECT--
Testing a connection with ATTR_PERSISTENT...
Exception from unsupported attribute (ATTR_PERSISTENT) is caught

Testing new connection after exception thrown in previous connection...
Test successfully completed
