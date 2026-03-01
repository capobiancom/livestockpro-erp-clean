<?php

namespace App\Enums;

enum PregnancyStatus: string
{
    case Ongoing = 'ongoing';
    case Confirmed = 'confirmed';
    case Abortion = 'abortion';
    case Embryonicdeath = 'embryonic_death';
    case Miscarriage = 'miscarriage';
    case Lost = 'lost';
    case Pregnancyloss = 'pregnancy_loss';
    case Aborted = 'aborted';
    case Completed = 'completed';
}
