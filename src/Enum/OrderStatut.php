<?php

namespace App\Enum;

enum OrderStatut: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Unpaid = 'Unpaid';
    case Cancelled = 'Cancelled';
    case Deleted = 'Deleted';
    case Delivered = 'Delivered';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Paid => 'Payée',
            self::Unpaid => 'Non Payée',
            self::Cancelled => 'Annulée',
            self::Deleted => 'Supprimée',
            self::Delivered => 'Livrée',
        };
    }
}