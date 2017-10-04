--TEST--
prepare with cursor buffered and fetch a money column with the column bound and specified as pdo type int
--SKIPIF--
<?php require('skipif_mid-refactor.inc'); ?>
--FILE--
<?php
require_once("MsCommon_mid-refactor.inc");

try {
    $conn = connect();
    $sample = 1234567890.1234;

    $tbname = "TESTTABLE";
    if (!isColEncrypted()) {
        createTable($conn, $tbname, array("exist", "money"));
    } else {
        // inserting money types is not supported for Always Encrypted; use decimal(19,4) instead
        createTable($conn, $tbname, array("exist" => "decimal(19,4)"));
    }

    $query = "INSERT INTO $tbname VALUES(:p0)";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':p0', $sample, PDO::PARAM_INT);
    $stmt->execute();

    $query = "SELECT exist FROM $tbname";

    //prepare with no buffered cursor
print "no buffered cursor, stringify off, fetch_numeric off\n"; //stringify and fetch_numeric is off by default
$stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    print "\nno buffered cursor, stringify off, fetch_numeric on\n";
    $conn->setAttribute(PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE, true);
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    print "\nno buffered cursor, stringify on, fetch_numeric on\n";
    $conn->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    print "\nno buffered cursor, stringify on, fetch_numeric off\n";
    $conn->setAttribute(PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE, false);
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    //prepare with client buffered cursor
    print "\nbuffered cursor, stringify off, fetch_numeric off\n";
    $conn->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL, PDO::SQLSRV_ATTR_CURSOR_SCROLL_TYPE => PDO::SQLSRV_CURSOR_BUFFERED));
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    print "\nbuffered cursor, stringify off, fetch_numeric on\n";
    $conn->setAttribute(PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE, true);
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL, PDO::SQLSRV_ATTR_CURSOR_SCROLL_TYPE => PDO::SQLSRV_CURSOR_BUFFERED));
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    print "\nbuffered cursor, stringify on, fetch_numeric on\n";
    $conn->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL, PDO::SQLSRV_ATTR_CURSOR_SCROLL_TYPE => PDO::SQLSRV_CURSOR_BUFFERED));
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    print "\nbuffered cursor, stringify on, fetch_numeric off\n";
    $conn->setAttribute(PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE, false);
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL, PDO::SQLSRV_ATTR_CURSOR_SCROLL_TYPE => PDO::SQLSRV_CURSOR_BUFFERED));
    $stmt->execute();
    $stmt->bindColumn('exist', $money_col, PDO::PARAM_INT);
    $value = $stmt->fetch(PDO::FETCH_BOUND);
    var_dump($money_col);

    dropTable($conn, $tbname);
    unset($stmt);
    unset($conn);
} catch (PDOException $e) {
    var_dump($e->errorInfo);
}
?>
--EXPECT--
no buffered cursor, stringify off, fetch_numeric off
int(1234567890)

no buffered cursor, stringify off, fetch_numeric on
int(1234567890)

no buffered cursor, stringify on, fetch_numeric on
string(10) "1234567890"

no buffered cursor, stringify on, fetch_numeric off
string(10) "1234567890"

buffered cursor, stringify off, fetch_numeric off
int(1234567890)

buffered cursor, stringify off, fetch_numeric on
int(1234567890)

buffered cursor, stringify on, fetch_numeric on
string(10) "1234567890"

buffered cursor, stringify on, fetch_numeric off
string(10) "1234567890"
