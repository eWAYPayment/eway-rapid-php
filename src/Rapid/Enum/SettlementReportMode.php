<?php

namespace Eway\Rapid\Enum;

/**
 * This defines the search modes available for settlement search.
 */
abstract class SettlementReportMode extends AbstractEnum
{
    const BOTH = 'Both';
    const SUMMARYONLY = 'SummaryOnly';
    const TRANSACTIONONLY = 'TransactionOnly';
}
