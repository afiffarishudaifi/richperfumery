<?PHP 
return [
    'rekening' => [
    	1 => 'BCA',
    	2 => 'MANDIRI'
    ],
    'carabayar' => [
        1 => 'Lunas',
    	2 => 'Uang Muka',
        3 => 'Hutang'
    ],
    'viabayar_outlet' => [
        1 => 'Tunai',
        2 => 'Debet',
        3 => 'Flazz',
        4 => 'Kredit',
        5 => 'Transfer',
        6 => 'OVO',
        7 => 'Hutang'
    ],
    'viabayar_pemilik' => [
        1 => 'Tunai',
        2 => 'Debet',
        3 => 'Kredit',
        4 => 'Transfer',
        5 => 'Hutang'
    ],
    'status_pembayaran' => [ 
        1 => 'Tunai',
        2 => 'Uang Muka',
        3 => 'Hutang'
    ],
    'status_penerimaan' => [ 
        1 => 'Belum Diterima',
        2 => 'Diterima Sebagian',
        3 => 'Sudah Diterima'
	],
	'status_penerimaanpengiriman' => [ 
        1 => 'Belum Diterima',
        2 => 'Sudah Diterima',
        3 => 'Diterima Sebagian'
    ],
    'status_penerimaanretur' => [ 
        1 => 'Belum Diterima',
        2 => 'Sudah Diterima',
        3 => 'Diterima Sebagian'
    ],

    'status_persetujuan' => [ 
        1 => 'Belum Diterima',
        2 => 'Sudah Diterima'
    ],
	'status_stok'=>[
		'P1' => 'Pembelian',
		'P2' => 'Pembelian Retur',
		'P3' => 'Pembelian Penerimaan',
		'K1' => 'Pengiriman Outlet',
		'K2' => 'Pengiriman Penerimaan Outlet',
		'K3' => 'Pengiriman retur Outlet',
		'K4' => 'Pengiriman Penerimaan retur Outlet',
		'J1' => 'Penjualan Outlet',
		'J2' => 'Penjualan Retur Outlet',
		'J3' => 'Penjualan Penerimaan Outlet',
		'J4' => 'Penjualan Grosir',
		'S1' => 'Stok Opname Bertambah',
        'S2' => 'Stok Opname Berkurang',
        'SA1'=> 'Saldo Awal',
        'R1' => 'Tukar Poin',
	],
	'hari' =>[
        '0' => 'Minggu',
        '1' => 'Senin',
        '2' => 'Selesa',
        '3' => 'Rabu',
        '4' => 'Kamis',
        '5' => 'Jumat',
        '6' => 'Sabtu'
    ],
    'jenis_transaksi'=>[
        '1' => 'Penjualan Outlet',
        '2' => 'Penjualan Grosir',
        '3' => 'Redeem Penjualan'
    ]
];
?>