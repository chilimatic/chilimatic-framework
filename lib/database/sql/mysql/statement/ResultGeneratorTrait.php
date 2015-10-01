<?php
/**
 *
 * @author j
 * Date: 9/29/15
 * Time: 10:01 PM
 *
 * File: ResultGeneratorTrait.php
 */

trait ResultGeneratorTrait {


    /**
     * @return Generator|null
     */
    public function objectGenerator()
    {
        switch (true) {
            case $this->getStatment() instanceof \PdoStatement:
                yield $this->getStatment()->fetchObject();
                break;
            case $this->getStatment() instanceof \mysqli_stmt:
                yield $this->getResult()->fetch_object();
                break;
        }

        return null;
    }


    /**
     * @return Generator|null
     */
    public function assocGenerator()
    {
        switch (true) {
            case $this->getStatment() instanceof \PdoStatement:
                yield $this->getStatment()->fetchObject();
                break;
            case $this->getStatment() instanceof \mysqli_stmt:
                yield $this->getResult()->fetch_assoc();
                break;
        }

        return null;
    }


    /**
     * @return Generator|null
     */
    public function numericGenerator()
    {
        switch (true) {
            case $this->getStatment() instanceof \PdoStatement:
                yield $this->getStatment()->fetchObject();
                break;
            case $this->getStatment() instanceof \mysqli_stmt:
                yield $this->getResult()->fetch_assoc();
                break;
        }

        return null;
    }

}