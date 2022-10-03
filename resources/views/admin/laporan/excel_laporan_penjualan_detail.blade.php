<table>
    <thead>
	<tr>   
        <th>NO</th>
        <th>TANGGAL JUAL</th>
        <th>NOMOR NOTA</th>
        <th>NAMA CUSTOMER</th>
        <th>NAMA PARFUM</th>
        <th>JUMLAH</th>
        <th>NAMA BARANG</th>
        <th>VOLUME BARANG</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $x => $value)
        <?php $tgl_jual ?>
        <?php $nomer_nota ?>

        @if(empty($value['sub']))
            <tr>
                <td>{{ $value['no'] }}</td>
                <td>{{ $value['tgl_jual'] }}</td>
                <td>{{ $value['nomer_nota'] }}</td>
                <td>{{ $value['nama_customer'] }}</td>
                <td>{{ $value['nama_parfum'] }}</td>
                <td>{{ intval($value['jumlah']) }}</td>
                <td> - </td>
                <td> - </td>
            </tr>
        @else
            @for ($i = 0;$i < count($value['sub']); $i++)
                @if ($i === 0)
                    <?php $tgl_jual = $value['tgl_jual'] ?>
                    <?php $nomer_nota = $value['nomer_nota'] ?>

                    <tr>
                        <td>{{ $value['no'] }}</td>
                        <td>{{ $value['tgl_jual'] === $tgl_jual && $value['nomer_nota'] === $nomer_nota ? $value['tgl_jual'] : '' }}</td>
                        <td>{{ $value['tgl_jual'] === $tgl_jual && $value['nomer_nota'] === $nomer_nota ? $value['nomer_nota'] : '' }}</td>
                        <td>{{ $value['nama_customer'] }}</td>
                        <td>{{ $value['nama_parfum'] }}</td>
                        <td>{{ intval($value['jumlah']) }}</td>
                        <td>{{ $value['sub'][0]['nama_barang'] }}</td>
                        <td>{{ $value['sub'][0]['volume_barang'] }}</td>
                    </tr>
                @else 
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $value['sub'][$i]['nama_barang'] }}</td>
                        <td>{{ $value['sub'][$i]['volume_barang'] }}</td>
                    </tr>
                @endif
            @endfor
        @endif
    @endforeach
    </tbody>
</table>
