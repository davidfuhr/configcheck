<?php

class MysqlSetting implements Setting
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var PDO
     */
    private $pdoConnection;

    /**
     * @var string $name
     * @var PDO $pdoConnection
     */
    public function __construct($name, PDO $pdoConnection)
    {
        $this->name = (string) $name;
        $this->pdoConnection = $pdoConnection;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return scalar
     */
    public function getValue()
    {
        $value = null;
        $stmt = $this->pdoConnection->query('SHOW variables LIKE ' . $this->pdoConnection->quote($this->name) .';');
        if ($stmt->rowCount() === 1) {
            $value = array_pop($stmt->fetch(PDO::FETCH_NUM));
        }
        return $value;
    }
}

