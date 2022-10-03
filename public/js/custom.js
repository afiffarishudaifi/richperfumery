$('.select2').select2({});
$('.no-symbol').keypress(function (e) {
    var txt = String.fromCharCode(e.which);
    if (!txt.match(/[A-Za-z0-9&.]/)) {
        return false;
    }
});

var date = new Date();
var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
var year = new Date(date.getFullYear())
$('.datepicker').datepicker({
    format : 'dd-mm-yyyy',
    setDate: new Date(),
    autoclose: true,
    clearBtn: true,
});

$('.number-only').keypress(function (e) {
    var txt = String.fromCharCode(e.which);
    if (!txt.match(/[0-9.,]/)) {
        return false;
    }
});

function disabled(id, status){
	$(id).attr('disabled', status);
}

function readonly(id, status){
    $(id).attr('readonly',status);
}

function changeCurrency(value, currency){
    accounting.settings = {
        currency: {
            symbol : currency,   // default currency symbol is '$'
            format: { pos : "%s%v", neg : "( %s%v )", zero : "%s%v"}, // controls output: %s = symbol, %v = value/number (can be object: see below)
            decimal : ",",  // decimal point separator
            thousand: ".",  // thousands separator
            precision : 2   // decimal places
        },
        number: {
            precision : 2,  // default precision on numbers is 0
            thousand: ".",
            decimal : ","
        }
    }

    return accounting.formatMoney(value);
}

function curr(x){
    var parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    var ret = parts.join(".");
    return 'Rp '+ret;
  }

function maksimal(x) {
        var min = parseFloat(x.attr('min'));
        var max = parseFloat(x.attr('max'));

        if(x.val() > max) {
            x.val(max);
        }else if(x.val() < min){
            x.val(min);
        }
    }

function openInNewTab(url) {
    var win = window.open(url, '_blank');
    win.focus();
}

function set_format(this_){ //help-block form
    var val = this_.val();
    if(val==""){
        val = 0;
    }
    var div = this_.parents("div");
    div.find(".txt-format").html(curr(val));
}

function formatDate(date) {
  var monthNames = [
    "January", "February", "March",
    "April", "May", "June", "July",
    "August", "September", "October",
    "November", "December"
  ];

  var day = date.getDate();
  var monthIndex = date.getMonth();
  var year = date.getFullYear();

  return day + ' - ' + monthIndex + ' - ' + year;
}

function parse_float(value){
    return parseFloat(value);
}

function parse_int(value){
    return parseInt(value);
}

function notif_checkbox(){
        swal({
            title: 'Tidak Ada Data Terpilih!',
            type: 'warning'
        })
    }

function number_test(n){
    var result = (n - Math.floor(n)) !== 0; 
    
    //decimal   
    if(result){
        return n.toFixed(2);
    }else{
        return n;
    }
}

function format_angka(val){
    var result = (val - Math.floor(val)) !== 0; 
    
    //decimal   
    // if(result){
    //     return numeral(val).format('0,0.00');
    // }else{
    //     return numeral(val).format('0,0');
    // }

    return accounting.formatNumber(val);   
}

function custom_swal(val, jenis){

    swal("", val, jenis);
}

function format_angka_nodesimal(value) {
    accounting.settings = {
        currency: {
            symbol: "Rp ",
            precision: 0,
            thousand: ".",
            decimal: ",",
            format: {
                pos: '%s %v',
                neg: '%s (%v)',
                zero: '%s %v'
            },
        },
        number: {
            precision: 0,  // default precision on numbers is 0
            thousand: ".",
            decimal: ","
        }
    }

    return accounting.formatNumber(value);
}

function format_uang(value) {
    accounting.settings = {
        currency: {
            symbol: "Rp ",
            precision: 2,
            thousand: ".",
            decimal: ",",
            format: {
                pos: '%s %v',
                neg: '%s (%v)',
                zero: '%s %v'
            },
        },
        number: {
            precision: 0,  // default precision on numbers is 0
            thousand: ".",
            decimal: ","
        }
    }

    return accounting.formatMoney(value);
}
