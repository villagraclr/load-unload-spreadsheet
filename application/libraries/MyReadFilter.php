<<<<<<< HEAD
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class MyReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    private $columns = [];

    public function __construct($startRow, $endRow, $columns)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->columns = $columns;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row >= $this->startRow && $row <= $this->endRow) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        }
        return false;
    }
}
=======
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class MyReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    private $columns = [];

    public function __construct($startRow, $endRow, $columns)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->columns = $columns;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row >= $this->startRow && $row <= $this->endRow) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        }
        return false;
    }
}
>>>>>>> 6836a0ba492d21770997b492d262c3d6bd556f1d
?>