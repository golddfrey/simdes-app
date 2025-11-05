<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * TrustProxies
 *
 * Middleware ini memberi tahu Laravel tentang header "X-Forwarded-*"
 * dari reverse proxy (Nginx/Load Balancer/CDN). Setting default ini aman
 * untuk development lokal. Jika nanti kamu deploy di balik proxy tertentu,
 * tinggal sesuaikan $proxies atau $headers.
 */
class TrustProxies extends Middleware
{
    /**
     * Daftar IP atau CIDR proxy yang dipercaya.
     * - null  : auto-detect (default)
     * - '*'   : percaya semua proxy (umum dipakai saat dev atau PaaS)
     * - array : contoh ['192.168.1.1', '10.0.0.0/8']
     *
     * Untuk dev lokal, null sudah cukup. Jika kamu ingin mengizinkan semua:
     * protected $proxies = '*';
     */
    protected $proxies = null;

    /**
     * Header yang digunakan untuk mendeteksi informasi forwarded.
     * Nilai di bawah ini adalah default skeleton Laravel terbaru.
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_AWS_ELB;
}
