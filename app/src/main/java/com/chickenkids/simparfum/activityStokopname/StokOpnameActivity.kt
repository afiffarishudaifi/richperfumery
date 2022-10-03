package com.chickenkids.simparfum.activityStokopname

import android.app.DatePickerDialog
import android.app.ProgressDialog
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import com.chickenkids.simparfum.R
import com.chickenkids.simparfum.Utils.ServiceClientApi
import com.chickenkids.simparfum.Utils.UtilNet
import com.chickenkids.simparfum.activityMain.MainActivity
import com.chickenkids.simparfum.modelStokopname.PersediaanResponse
import kotlinx.android.synthetic.main.activity_stok_opname.*
import org.jetbrains.anko.startActivity
import java.text.SimpleDateFormat
import java.util.*


class StokOpnameActivity : AppCompatActivity(),
    StokOpnameView {

    private  var listDataPersediaan : MutableList<PersediaanResponse> = mutableListOf()
    private lateinit var api: ServiceClientApi
    private var utilNet = UtilNet()
    private lateinit var presenter: StokOpnamePresenter
    var dialog: ProgressDialog? = null

    private var detail_barang :String ? = null
    private var id_gudang :String ? = null
    val myCalendar = Calendar.getInstance()
    var intStok:Int?=null
    var intFisik:Int?=null

    override fun showLoading() {
        dialog = ProgressDialog.show(this, "","Please Wait...", true)
    }
    override fun hideLoading() {
        dialog!!.dismiss()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_stok_opname)
        datePicker()

        val data = intent
        detail_barang = data.getStringExtra("detail_barang") //ambil dari barcodeactivity.kt
        id_gudang = data.getStringExtra("id_gudang")
//        detail_barang isine opo kxk iki podo
//        val s = "255||Calvin Klein||7||Mililiter"
        val st = StringTokenizer(detail_barang, "||")

        val id_barang = st.nextToken() //255
        val nm_barang = st.nextToken() //calvin
        val id_satuan = st.nextToken() //7
        val nm_satuan = st.nextToken() //mili
        val id_d_gudang = id_gudang

        //ngunu isine,. mksd e iso ngnu ??
        // iyo iku wes iso.. tp lak gawe split ngene

        val stSplit = detail_barang!!.split("||")
        val idx = stSplit.get(0) // gawe array tanda !! kui string to ?
        /*Kotlin dwe 2 jenis type data paten 1 oleh null, 1 g oleh null
//        lek oleh null mesti di akhiri ? trs nanti pemanggilane !!. atau ?.
//lek g oleh null ya biasa ne
//contoh
        * */

        val nadaNull :String? =""
        var nadaNotNull :String =""

        /* tre eneng neh val karo var
//        lak val iku g iso di ganti nilaine (VALUE)
//lak var iku iso diganti (VARIABLE)
    suwun2 mastah atash penjelasanny hehe..
        * */



//        System.out.println(id_satuan)
//        Toast.makeText(this, id_d_gudang.toString(), Toast.LENGTH_SHORT).show()

        /***/
        api = utilNet.getService().create(ServiceClientApi::class.java)
        presenter =
            StokOpnamePresenter(
                this,
                api
            )
        presenter.getDataPersediaan("$id_barang","$id_satuan","$id_d_gudang")
        /***/

        val currentDate = SimpleDateFormat("dd-MM-yyyy", Locale.getDefault()).format(Date())

        et_id_barang.setText("$id_barang")
//        et_nm_barang.setText("$nm_barang")
        et_id_satuan.setText("$id_satuan")
//        et_nm_satuan.setText("$nm_satuan")
        et_jml_stok.setText("")
        et_jml_fisik.setText("")
        et_jml_selisih.setText("")
        et_tanggal.setText("$currentDate")
        et_id_gudang.setText("$id_gudang")
        et_keterangan.setText("")
        addKeyListener()

        btn_simpan.setOnClickListener{
            if (et_nm_barang.text!!.toString().equals("") && et_nm_satuan.text!!.toString().equals("") && et_jml_stok.text!!.toString().equals("") && et_jml_selisih.text!!.toString().equals("")){
                AlertDialog.Builder(this)
                    .setIcon(android.R.drawable.ic_dialog_alert)
                    .setTitle("Error!")
                    .setMessage("(*) Wajib diisi!")
                    .setCancelable(false)
                    .setNegativeButton("Close!") { _, _ ->
                        finish()
                    }
                    .show()
            }else{
                presenter.simpanStokOpname(
                    "","${et_tanggal.text}","${et_id_barang.text}","${et_id_gudang.text}","${et_jml_stok.text}","${et_jml_fisik.text}","${et_jml_selisih.text}","${et_id_satuan.text}","${et_keterangan.text}",""
                )

            }
        }
    }

    private fun addKeyListener() {
        et_jml_fisik.addTextChangedListener(object : TextWatcher {
            override fun onTextChanged(
                s: CharSequence, start: Int, before: Int,
                count: Int
            ) {
                if (s != "") {
                    //do your work here
                    if (et_jml_fisik.text.toString()==""){
                        intStok = Integer.parseInt(et_jml_stok.text.toString())
                        et_jml_selisih.setText("$intStok")
                    }else{
                        intStok = Integer.parseInt(et_jml_stok.text.toString())
                        intFisik= Integer.parseInt(et_jml_fisik.text.toString())
                        val sum = intStok!! - intFisik!!
                        et_jml_selisih.setText(sum.toString())
                    }
                }
            }
            override fun beforeTextChanged(
                s: CharSequence, start: Int, count: Int,
                after: Int
            ) {
            }
            override fun afterTextChanged(s: Editable) {
            }
        })
    }

    override fun showDataPersediaan(data: PersediaanResponse) {
        listDataPersediaan.clear()
        listDataPersediaan.add(data)
        System.out.println(data)
        et_nm_barang.setText("${data.result!!.namaBarang}")
        et_id_satuan.setText("${data.result!!.idSatuan}")
        et_nm_satuan.setText("${data.result!!.namaSatuan}")
        et_jml_stok.setText("${data.result!!.stok}")
        et_jml_selisih.setText("${data.result!!.stok}")
    }

    override fun errorDataPersediaan(message: String) {
        AlertDialog.Builder(this)
            .setIcon(android.R.drawable.ic_dialog_alert)
            .setTitle("Error!")
            .setMessage("Data QR Code tidak valid!")
            .setCancelable(false)
            .setNegativeButton("Scan Ulang!") { _, _ ->
                finish()
            }
            .show()
    }

    override fun responseSaveStokOpname(code:String,message:String) {
        var title: String? = if (code == "1"){
            "Sukses"
        }else{
            "Gagal"
        }

        if (code=="1"){
            AlertDialog.Builder(this)
                .setIcon(android.R.drawable.ic_dialog_alert)
                .setTitle("$title!")
                .setMessage(message)
                .setCancelable(false)
                .setNegativeButton("Ok!") { _, _ ->
                    startActivity<MainActivity>(
                        "codeIntent" to "2"
                    )
                }
                .show()
        }else{
            AlertDialog.Builder(this)
                .setIcon(android.R.drawable.ic_dialog_alert)
                .setTitle("$title!")
                .setMessage(message)
                .setCancelable(false)
                .setPositiveButton("Coba Lagi") { _, _ ->

                }
                .setNegativeButton("Batal StokOpname") { _, _ ->
                    startActivity<MainActivity>(
                        "codeIntent" to "2"
                    )
                }
                .show()
        }
    }

    private fun datePicker(){
        val date = DatePickerDialog.OnDateSetListener { _, year, monthOfYear, dayOfMonth ->
            myCalendar.set(Calendar.YEAR, year)
            myCalendar.set(Calendar.MONTH, monthOfYear)
            myCalendar.set(Calendar.DAY_OF_MONTH, dayOfMonth)
            updateLabel()
        }
        et_tanggal.setOnClickListener {
            DatePickerDialog(this, date, myCalendar.get(Calendar.YEAR), myCalendar.get(Calendar.MONTH),
                myCalendar.get(Calendar.DAY_OF_MONTH)
            ).show()
        }
    }

    private fun updateLabel() {
        val myFormat = "dd-MM-yyyy"
        val sdf = SimpleDateFormat(myFormat, Locale.US)
        et_tanggal.setText(sdf.format(myCalendar.time))
    }

}
