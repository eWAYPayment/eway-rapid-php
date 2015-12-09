<?php

namespace Eway\Rapid\Model;

/**
 * Class Verification.
 *
 * @property string $CVN         Result of CVN Verification by card processor
 * @property string $Address     Result of Address Verification by card processor
 * @property string $Email       Result of email verification by card processor
 * @property string $Mobile      Result of Mobile verification by card processor
 * @property string $Phone       Result of phone verification by card processor
 * @property string $BeagleEmail Result of email verification from responsive shared page
 * @property string $BeaglePhone Result of phone number verification from responsive shared page
 */
class Verification extends AbstractModel
{
    protected $fillable = [
        'CVN',
        'Address',
        'Email',
        'Mobile',
        'Phone',
    ];

    /**
     * @param string $cvn
     *
     * @return $this
     */
    public function setCVNAttribute($cvn)
    {
        $this->validateEnum('Eway\Rapid\Enum\VerifyStatus', 'CVN', $cvn);

        return $this;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddressAttribute($address)
    {
        $this->validateEnum('Eway\Rapid\Enum\VerifyStatus', 'Address', $address);

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmailAttribute($email)
    {
        $this->validateEnum('Eway\Rapid\Enum\VerifyStatus', 'Email', $email);

        return $this;
    }

    /**
     * @param string $mobile
     *
     * @return $this
     */
    public function setMobileAttribute($mobile)
    {
        $this->validateEnum('Eway\Rapid\Enum\VerifyStatus', 'Mobile', $mobile);

        return $this;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhoneAttribute($phone)
    {
        $this->validateEnum('Eway\Rapid\Enum\VerifyStatus', 'Phone', $phone);

        return $this;
    }
}
