<?php

namespace Eway\Rapid\Model;

/**
 * Class SettlementSearch.
 *
 * @property string $ReportMode      One of Both, SummaryOnly or TransactionOnly
 * @property string $SettlementDate  A settlement date will need to be entered to query.
 *                                   This should be formatted as YYYY-MM-DD. Use this or StartDate & EndDate
 * @property string $StartDate       This parameter set the start of a filtered date range.
 *                                   This should be formatted as YYYY-MM-DD. Use this or SettlementDate
 * @property string $EndDate         This parameter set the end of a filtered date range.
 *                                   This should be formatted as YYYY-MM-DD. Use this or SettlementDate
 * @property string $CardType        The code for the card type to filter by. One of: ALL, VI, MC,
 *                                   AX, DC, JC, MD, MI, SO, LA, DS
 * @property string $Currency        The currency to filter the report by. The three digit ISO 4217
 *                                   currency code should be used or ALL for all currencies.
 * @property int    $Page            The page number to retrieve
 * @property int    $PageSize        The number of records to retrieve per page
 */
class SettlementSearch extends AbstractModel
{
    protected $fillable = [
        'ReportMode',
        'SettlementDate',
        'StartDate',
        'EndDate',
        'CardType',
        'Currency',
        'Page',
        'PageSize',
    ];
}
