<?php

namespace HMS\Entities\Phones;

use HMS\Entities\User;

class PhoneExtension
{
    /**
     * The phone number for the extension.
     *
     * @var string
     */
    protected $extension;

    /**
     * The phoneword for the extension (e.g. HACK for 4225).
     *
     * @var string
     */
    protected $phoneword;

    /**
     * The user which owns the extension.
     *
     * @var User
     */
    protected $user;

    /**
     * A description of the extension, as displayed on the directory page.
     *
     * @var string
     */
    protected $description;

    /**
     * The type of extension (e.g. DECT, SIP or POTS).
     *
     * @var string|ExtensionType
     */
    protected $type;

    /**
     * For SIP extensions, the password for SIP registration.
     *
     * @var string
     */
    protected $sipPassword;

    /**
     * For POTS and DECT extensions, the real number which the extension is mapped to.
     *
     * @var string
     */
    protected $mappedNumber;

    /**
     * Get the extension number.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the extension number.
     *
     * @param string $extension
     *
     * @return PhoneExtension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get the phoneword representation of the number.
     *
     * @return string
     */
    public function getPhoneword()
    {
        return strtoupper($this->phoneword);
    }

    /**
     * Set the extension number.
     *
     * @param string $extension
     *
     * @return PhoneExtension
     */
    public function setPhoneword($phoneword)
    {
        $this->phoneword = $phoneword;

        return $this;
    }

    /**
     * Get the User which owns this extension.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the extension number.
     *
     * @param string $extension
     *
     * @return PhoneExtension
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the extension description (who, what, etc).
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the extension's description.
     *
     * @param string $description
     *
     * @return PhoneExtension
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the extension type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get a descriptive version of the extension type.
     *
     * @return string
     */
    public function getTypeString()
    {
        return ExtensionType::TYPE_STRINGS[$this->type];
    }

    /**
     * Set the extension type (e.g. DECT, SIP, POTS).
     *
     * @param string $type
     *
     * @return PhoneExtension
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the SIP registration for this extension.
     *
     * @return string
     */
    public function getSipPassword()
    {
        return $this->sipPassword;
    }

    /**
     * Generate a random password for the SIP endpoint.
     *
     * @return PhoneExtension
     */
    public function generateSipPassword()
    {
        $this->sipPassword = uniqid();

        return $this;
    }

    /**
     * Set the SIP registration password.
     *
     * @param string $sipPassword
     *
     * @return PhoneExtension
     */
    public function setSipPassword($sipPassword)
    {
        $this->sipPassword = $sipPassword;

        return $this;
    }

    /**
     * Get the number which this extension is mapped to.
     *
     * @return string
     */
    public function getMappedNumber()
    {
        return $this->mappedNumber;
    }

    /**
     * Set the number which this extension is mapped to.
     *
     * @param string $mappedNumber
     *
     * @return PhoneExtension
     */
    public function setMappedNumber($mappedNumber)
    {
        $this->mappedNumber = $mappedNumber;
    }
}
