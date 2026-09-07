<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function userget()
    {
        $nombre = 'Jose Perez';
        $activeMembers = 1842;
        $monthlyRevenue = 42390;
        $attendanceRate = 84.2;
        $newSignups = 158;
        $maintenanceItems = collect([
            (object) ['code' => 'EQ-09', 'name' => 'Treadmill Zone 2', 'issue' => 'Drive Belt Slipping', 'level' => 'critical', 'time' => '55m ago'],
            (object) ['code' => 'EQ-44', 'name' => 'Cable Crossover A', 'issue' => 'Frayed Pulley Cable', 'level' => 'warn', 'time' => '1h ago'],
            (object) ['code' => 'EQ-12', 'name' => 'Leg Press Station', 'issue' => 'Hydraulic Cylinder Leak', 'level' => 'warn', 'time' => '3d ago'],
        ]);
        $classes = collect([
            (object) ['time' => '06:00 AM', 'class' => 'Tactical Conditioning', 'trainer' => 'Marcus Vance', 'attendance' => '24/25', 'status' => 'full'],
            (object) ['time' => '08:30 AM', 'class' => 'Iron Barbell Olympic', 'trainer' => 'Sarah Connor', 'attendance' => '12/15', 'status' => 'active'],
            (object) ['time' => '12:00 PM', 'class' => 'Combat HIIT Conditioning', 'trainer' => 'John Kreese', 'attendance' => '18/20', 'status' => 'active'],
            (object) ['time' => '05:30 PM', 'class' => 'Power-Lifting Method', 'trainer' => 'Marcus Vance', 'attendance' => '08/12', 'status' => 'pending'],
        ]);
        $transactions = collect([
            (object) ['name' => 'Logan Cole', 'plan' => 'Black Pass Annual · VISA', 'amount' => '149.00', 'time' => '09:42 AM'],
            (object) ['name' => 'Diana Prince', 'plan' => 'Standard Monthly · APPLE PAY', 'amount' => '79.00', 'time' => '09:11 AM'],
            (object) ['name' => 'Roy Harper', 'plan' => 'Performance Tier · MASTERCARD', 'amount' => '119.00', 'time' => '08:50 AM'],
            (object) ['name' => 'Selina Kyle', 'plan' => 'Black Pass Monthly · VISA', 'amount' => '149.00', 'time' => '07:22 AM'],
        ]);
        $growthLabels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        $growthData = [1200, 1260, 1310, 1350, 1400, 1430, 1500, 1520, 1580, 1650, 1730, 1842];

        return view('user', compact(
            'nombre',
            'activeMembers',
            'monthlyRevenue',
            'attendanceRate',
            'newSignups',
            'maintenanceItems',
            'classes',
            'transactions',
            'growthLabels',
            'growthData',
        ));
    }
}
