<?php

namespace App\Controllers;

use App\Models\AduanModel;

class Dashboard extends BaseController
{
    protected $aduanModel;

    public function __construct()
    {
        $this->aduanModel = new AduanModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        $chartData = null;

        if ($role === 'admin') {
            $total = $this->aduanModel->countAllResults();
            $pending = $this->aduanModel->where('status', 'Pending')->countAllResults();
            $proses = $this->aduanModel->where('status', 'Proses')->countAllResults();
            $selesai = $this->aduanModel->where('status', 'Selesai')->countAllResults();

            // Recent complaints
            $recentAduan = $this->aduanModel
                ->select('aduan.*, users.fullname')
                ->join('users', 'users.id = aduan.user_id')
                ->orderBy('aduan.created_at', 'DESC')
                ->limit(5)
                ->find();

            // Chart Data Calculations (Last 30 days)
            $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
            $aduanFilter = $this->aduanModel
                ->select('created_at, kategori')
                ->where('created_at >=', $thirtyDaysAgo)
                ->findAll();

            // Daily trend (last 7 days)
            $trendData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $trendData[$date] = 0;
            }
            foreach ($aduanFilter as $a) {
                $date = date('Y-m-d', strtotime($a['created_at']));
                if (isset($trendData[$date])) {
                    $trendData[$date]++;
                }
            }

            // Categories breakdown
            $kategoriData = [
                'Pelayanan' => 0,
                'Fasilitas' => 0,
                'Konsumsi'  => 0,
                'Lainnya'   => 0
            ];
            foreach ($aduanFilter as $a) {
                $cat = ucfirst(strtolower(trim($a['kategori'])));
                if (isset($kategoriData[$cat])) {
                    $kategoriData[$cat]++;
                } else {
                    $kategoriData['Lainnya']++;
                }
            }

            // Format dates labels for human readability (e.g. 23 Jun)
            $formattedLabels = [];
            foreach (array_keys($trendData) as $dateStr) {
                $formattedLabels[] = date('d M', strtotime($dateStr));
            }

            $chartData = [
                'trend' => [
                    'labels' => $formattedLabels,
                    'values' => array_values($trendData),
                ],
                'kategori' => [
                    'labels' => array_keys($kategoriData),
                    'values' => array_values($kategoriData),
                ]
            ];
        } else {
            $total = $this->aduanModel->where('user_id', $userId)->countAllResults();
            $pending = $this->aduanModel->where('user_id', $userId)->where('status', 'Pending')->countAllResults();
            $proses = $this->aduanModel->where('user_id', $userId)->where('status', 'Proses')->countAllResults();
            $selesai = $this->aduanModel->where('user_id', $userId)->where('status', 'Selesai')->countAllResults();

            // Recent complaints for user
            $recentAduan = $this->aduanModel
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->find();
        }

        $data = [
            'title'        => 'Dashboard - SIMA BAPELKES',
            'total'        => $total,
            'pending'      => $pending,
            'proses'       => $proses,
            'selesai'      => $selesai,
            'recent_aduan' => $recentAduan,
            'chartData'    => $chartData,
        ];

        return view('dashboard/index', $data);
    }
}
