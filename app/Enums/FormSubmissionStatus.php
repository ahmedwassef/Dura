<?php

namespace App\Enums;

enum FormSubmissionStatus: string
{
    case Completed = 'completed';
    case PendingAdmin = 'pending_admin';
    case PendingDiscountReview = 'pending_discount_review';
}
