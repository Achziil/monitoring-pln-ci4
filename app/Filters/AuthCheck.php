<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $level = $session->get('level');
        $busa = $session->get('busa'); // Mengambil 'busa' dari sesi

        if ($arguments === null || $arguments[0] === 'guest') {
            if ($session->get('logged_in')) {
                switch ($level) {
                    case 'admin':
                        return redirect()->to('/admin/dashboard');
                    case 'wilayah':
                        return redirect()->to('/wilayah/dashboard');
                    case 'pelaksana':
                        return redirect()->to('/pelaksana/dashboard');
                    default:
                        return redirect()->to('/login');
                }
            }
        } else {
            if (!$session->get('logged_in')) {
                return redirect()->to('/login')->with('error', 'Anda harus login terlebih dahulu untuk mengakses halaman ini.');
            }

            if (!in_array($level, $arguments)) {
                return redirect()->to('/')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melihat halaman ini.');
            }

            // Memasukkan 'busa' ke dalam request agar dapat diakses di controller.
            $request->busa = $busa;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the request
    }
}
