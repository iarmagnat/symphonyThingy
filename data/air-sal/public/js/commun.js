$(document).ready(function(){

    $('#reservation_Salle').on('change',function(){
        console.log("toto");
        $.ajax({
            url : "{{ path('salle_prestations' , {'id' : $(this).val() } ) }}",
            type : 'GET',
            success : function(datas, statut){
                console.log(datas.msg);
            }
        });
    })

   
    function printData()
    {
        var divToPrint=document.getElementById("printable");
        newWin= window.open("");
        newWin.document.write(divToPrint.outerHTML);
        newWin.print();
        newWin.close();
    }

    $('#print').on('click',function(){
        printData();
    })

});