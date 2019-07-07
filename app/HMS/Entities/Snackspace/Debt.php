<?php

namespace HMS\Entities\Snackspace;

use Carbon\Carbon;

class Debt
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Carbon
     */
    protected $auditTime;

    /**
     * @var null|int
     */
    protected $totalDebt;

    /**
     * @var null|int
     */
    protected $currentDebt;

    /**
     * @var null|int
     */
    protected $exDebt;

    /**
     * @var null|int
     */
    protected $totalCredit;

    /**
     * @var null|int
     */
    protected $currentCredit;

    /**
     * @var null|int
     */
    protected $exCredit;

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Carbon
     */
    public function getAuditTime()
    {
        return $this->auditTime;
    }

    /**
     * @param Carbon $auditTime
     *
     * @return self
     */
    public function setAuditTime(Carbon $auditTime)
    {
        $this->auditTime = $auditTime;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getTotalDebt()
    {
        return $this->totalDebt;
    }

    /**
     * @param null|int $totalDebt
     *
     * @return self
     */
    public function setTotalDebt($totalDebt)
    {
        $this->totalDebt = $totalDebt;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCurrentDebt()
    {
        return $this->currentDebt;
    }

    /**
     * @param null|int $currentDebt
     *
     * @return self
     */
    public function setCurrentDebt($currentDebt)
    {
        $this->currentDebt = $currentDebt;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getExDebt()
    {
        return $this->exDebt;
    }

    /**
     * @param null|int $exDebt
     *
     * @return self
     */
    public function setExDebt($exDebt)
    {
        $this->exDebt = $exDebt;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getTotalCredit()
    {
        return $this->totalCredit;
    }

    /**
     * @param null|int $totalCredit
     *
     * @return self
     */
    public function setTotalCredit($totalCredit)
    {
        $this->totalCredit = $totalCredit;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCurrentCredit()
    {
        return $this->currentCredit;
    }

    /**
     * @param null|int $currentCredit
     *
     * @return self
     */
    public function setCurrentCredit($currentCredit)
    {
        $this->currentCredit = $currentCredit;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getExCredit()
    {
        return $this->exCredit;
    }

    /**
     * @param null|int $exCredit
     *
     * @return self
     */
    public function setExCredit($exCredit)
    {
        $this->exCredit = $exCredit;

        return $this;
    }
}
