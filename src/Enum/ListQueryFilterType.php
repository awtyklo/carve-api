<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Enum;

enum ListQueryFilterType: string
{
    case BOOLEAN = 'boolean';
    case EQUAL = 'equal';
    case EQUALMULTIPLE = 'equalMultiple';
    case LIKE = 'like';
    case STARTSWITH = 'startsWith';
    case ENDSWITH = 'endsWith';
    case GREATERTHAN = 'greaterThan';
    case GREATERTHANOREQUAL = 'greaterThanOrEqual';
    case LESSTHAN = 'lessThan';
    case LESSTHANOREQUAL = 'lessThanOrEqual';
    case DATEGREATERTHAN = 'dateGreaterThan';
    case DATEGREATERTHANOREQUAL = 'dateGreaterThanOrEqual';
    case DATELESSTHAN = 'dateLessThan';
    case DATELESSTHANOREQUAL = 'dateLessThanOrEqual';
    case DATETIMEGREATERTHAN = 'dateTimeGreaterThan';
    case DATETIMEGREATERTHANOREQUAL = 'dateTimeGreaterThanOrEqual';
    case DATETIMELESSTHAN = 'dateTimeLessThan';
    case DATETIMELESSTHANOREQUAL = 'dateTimeLessThanOrEqual';
    case TIMEGREATERTHAN = 'timeGreaterThan';
    case TIMEGREATERTHANOREQUAL = 'timeGreaterThanOrEqual';
    case TIMELESSTHAN = 'timeLessThan';
    case TIMELESSTHANOREQUAL = 'timeLessThanOrEqual';
}
