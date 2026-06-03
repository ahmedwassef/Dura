<?php

namespace App\Enums;

enum FormSubmissionStatus: string
{
    case Completed = 'completed';
    case Pending = 'pending';
    case PendingAdmin = 'pending_admin';
    case PendingDiscountReview = 'pending_discount_review';
}
