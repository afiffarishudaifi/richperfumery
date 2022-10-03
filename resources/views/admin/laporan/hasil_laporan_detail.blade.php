<table id="oke" border="1">
    <thead>
	<tr>
	    <th>NO</th>
        <th>TANGGAL JUAL</th>
        <th>NOMER NOTA</th>
        <th>NAMA CUSTOMER</th>
        <th>NAMA PARFUM</th>
        <th>JUMLAH</th>
        <th>BARANG</th>
        <th>JUMLAH BARANG</th>
	    
    	</tr>    
    </thead>
    <tbody>
<?php
$no = 1;
if($data != null){ ?>
@foreach($data as $key => $row)

<tr>
    
    <td rowspan="{{ isset($row[0]) ? count($row[0]) : 1 }}">{{ $no++ }}</td>
    <td rowspan="{{ isset($row[0]) ? count($row[0]) : 1 }}">{{ $row['tgl_jual']}}</td>
    <td rowspan="{{ isset($row[0]) ? count($row[0]) : 1 }}">{{ $row['nomer_nota']}}</td>
    <td rowspan="{{ isset($row[0]) ? count($row[0]) : 1 }}">{{ $row['nama_customer']}}</td>
    <td rowspan="{{ isset($row[0]) ? count($row[0]) : 1 }}">{{ $row['nama_parfum']}}</td>
  
    <td rowspan="{{ isset($row[0]) ? count($row[0]) : 1 }}">{{ $row['jumlah']}}</td>
    

    @if(isset($row[0]))
        @foreach($row[0] as $i => $k)

            @if($i === 0)
                <td> {{ $k['nama_barang'] }}  </td>
                <td> {{ $k['volume_barang'] }}  </td>
            @else
                <tr>
                <td> {{ $k['nama_barang'] }}  </td>
                <td> {{ $k['volume_barang'] }}  </td>
               </tr>
            @endif
                
        @endforeach
        
    @else
    <td> - </td>
    <td> - </td>
    @endif
    
</tr>
@endforeach

   <?php  } else {?>
    <tr>
        <td colspan="9" align="center">Data kosong</td>


    </tr>

    <?php } ?>
  
    </tbody>
</table>
