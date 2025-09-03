import{r as s}from"./routes-D3ctfpXk.js";import{f as r,a as d}from"./format-B7a5NblC.js";import"./parse-B3pZ8sEA.js";const o={datatable:()=>s("transaksi_delivery_orders.data"),getSalesOrders:()=>s("transaksi_sales_orders.getSalesOrders"),getArea:()=>s("biteship.areas"),cekOngkir:()=>s("biteship.cek-ongkir")};class c{constructor(){this.pageName="Delivery Orders",this.datatableEl=$("#datatables"),this.soSelectEl=$("#selectSalesOrder"),this.areaSelectEls=$("#origin, #destination"),this.detailWrapper=document.querySelector("#so-details-wrapper"),this.btnCekOngkir=document.querySelector("#btnCekOngkir")}initIndex(){console.log(`Halaman ${this.pageName} Index berhasil dimuat!`),this.initDataTable()}initCreate(){console.log(`Halaman ${this.pageName} Create berhasil dimuat!`),this.initForm()}initEdit(){console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`),this.initForm()}initShow(){console.log(`Halaman ${this.pageName} Show berhasil dimuat!`)}initDataTable(){this.datatableEl.DataTable({processing:!0,serverSide:!0,ajax:o.datatable(),columns:[{data:"DT_RowIndex",name:"DT_RowIndex",className:"text-center",orderable:!1,searchable:!1},{data:"id",name:"id",visible:!1},{data:"no_do",name:"no_do",className:"text-center"},{data:"no_so",name:"no_so",className:"text-center"},{data:"tanggal",name:"tanggal",className:"text-center"},{data:"customer",name:"customer"},{data:"metode_pembayaran",name:"metode_pembayaran",className:"text-center"},{data:"total_qty",name:"total_qty",className:"text-end"},{data:"total_diskon",name:"total_diskon",className:"text-end",render:$.fn.dataTable.render.number(".",",",0,"Rp ")},{data:"grand_total",name:"grand_total",className:"text-end fw-bold",render:$.fn.dataTable.render.number(".",",",0,"Rp ")},{data:"approval_level",name:"approval_level"},{data:"status",name:"status"},{data:"keterangan",name:"keterangan"},{data:null,orderable:!1,searchable:!1,className:"text-center no-export",render:function(e,a,t){let n="";return t.can_show&&(n+=`
                            <a href="${t.show_url}" class="btn btn-sm rounded-4 btn-info" data-bs-toggle="tooltip" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        `),t.can_approve&&(t.approval_level==0?n+=`
                                    <form action="${t.approve_url}" method="POST" class="d-inline form-approval">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                        <button type="submit" class="btn btn-sm rounded-4 btn-success btn-approve" data-bs-toggle="tooltip" title="Ajukan">
                                            <i class="bi bi-check2-square"></i>
                                        </button>
                                    </form>
                                `:n+=`
                                    <div class="dropdown dropstart d-inline">
                                        <button class="btn btn-sm rounded-4 btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Action">
                                            <i class="bi bi-gear-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form action="${t.revisi_url}" method="POST" class="form-revisi">
                                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                                    <button type="submit" class="dropdown-item text-warning btn-revisi">
                                                        <i class="bi bi-arrow-clockwise"></i> Revisi
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="${t.approve_url}" method="POST" class="form-approval">
                                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                                    <button type="submit" class="dropdown-item text-success btn-approve">
                                                        <i class="bi bi-check2-square"></i> Approve
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="${t.reject_url}" method="POST" class="form-reject">
                                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                                    <button type="button" class="dropdown-item text-danger btn-reject">
                                                        <i class="bi bi-x-square-fill"></i> Reject
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                `),t.can_modify&&(t.can_edit&&(n+=`
                                    <a href="${t.edit_url}" class="btn btn-sm rounded-4 btn-warning" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                `),t.can_delete&&(n+=`
                                    <form action="${t.delete_url}" method="POST" class="d-inline form-delete">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-sm rounded-4 btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                `)),`<div class="d-flex justify-content-center gap-1 flex-wrap">${n}</div>`}}],dom:"<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-6 mb-3 mb-md-0 d-flex justify-content-center align-items-center'><'col-sm-12 col-md-3 text-right'f>><'row py-2'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",paging:!0,responsive:!0,pageLength:20,lengthMenu:[[20,50,-1],[20,50,"Semua"]],order:[[1,"desc"]],columnDefs:[{targets:0,className:"text-center"}],info:!0,language:{sEmptyTable:"Tidak ada data yang tersedia di tabel",sInfo:"Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",sInfoEmpty:"Menampilkan 0 hingga 0 dari 0 entri",sInfoFiltered:"(disaring dari _MAX_ entri keseluruhan)",sInfoPostFix:"",sInfoThousands:".",sLengthMenu:"Tampilkan _MENU_ entri",sLoadingRecords:"Memuat...",sProcessing:"Sedang memproses...",sSearch:"Cari:",sZeroRecords:"Tidak ditemukan data yang cocok",oAria:{sSortAscending:": aktifkan untuk mengurutkan kolom secara menaik",sSortDescending:": aktifkan untuk mengurutkan kolom secara menurun"}},drawCallback:function(){$('[data-bs-toggle="tooltip"]').each(function(){new bootstrap.Tooltip(this)})}})}initForm(){this.setupSoSelect(),this.setupAreaSelect(),this.btnCekOngkir.addEventListener("click",()=>this.checkOngkir());const e=document.getElementById("shipping-init-data");if(e)try{const a=JSON.parse(e.dataset.shippings);this.renderOngkirTable(a),this.saveOngkirDataToForm(a)}catch(a){console.error("Gagal parse shippings:",a)}}setupSoSelect(){this.soSelectEl.select2({theme:"bootstrap-5",placeholder:"Pilih Sales Order…",allowClear:!0,ajax:{url:o.getSalesOrders(),dataType:"json",delay:250,data:e=>({q:e.term,page:e.page||1}),processResults:(e,a)=>({results:e.results,pagination:{more:e.pagination.more}}),cache:!0},templateResult:({loading:e,no_so:a,tanggal:t,kode_customer:n,nama_toko:i})=>e?"Mencari…":$(`
                        <div>
                        <strong>${a}</strong> – ${t}<br>
                        <small class="text-muted">${n} | ${i}</small>
                        </div>
                    `),templateSelection:e=>e.no_so||e.text}).on("select2:select",async e=>{const a=e.params.data.id;if(a)try{const t=await fetch(s("transaksi_sales_orders.getSalesOrderDetail",{salesOrder:a})).then(n=>n.json());this.fillSoHeader(t),this.fillSoDetails(t)}catch(t){console.error("Gagal load SO detail:",t)}}).on("select2:clear",()=>{this.clearSoDetails()})}setupAreaSelect(){this.areaSelectEls.select2({theme:"bootstrap-5",placeholder:"Cari wilayah…",allowClear:!0,minimumInputLength:3,ajax:{url:o.getArea(),dataType:"json",delay:300,data:e=>({q:e.term}),processResults:e=>({results:e.areas.map(a=>({id:a.id,text:a.name}))}),cache:!0}}).on("select2:select",function(e){document.getElementById(`${this.id}_name`).value=e.params.data.text}).on("select2:clear",function(){document.getElementById(`${this.id}_name`).value=""})}async checkOngkir(){const e=document.getElementById("origin").value,a=document.getElementById("destination").value,t=Array.from(document.querySelectorAll(".product-row")).map(n=>({name:n.dataset.nama,description:n.dataset.description,value:+n.dataset.value,length:+n.dataset.length,width:+n.dataset.width,height:+n.dataset.height,weight:+n.dataset.weight,quantity:+n.dataset.qty})).filter(n=>n.quantity>0);if(!e||!a||t.length===0)return showToast(!e||!a?"Silakan pilih asal & tujuan pengiriman dulu.":"Tidak ada item valid untuk menghitung ongkir.","info");document.getElementById("harga_ongkir_list").innerHTML=`
      <div class="d-flex justify-content-center py-4">
        <div class="spinner-border" role="status"></div>
        <span class="ms-2">Loading ongkir…</span>
      </div>`;try{const n=document.querySelector('meta[name="csrf-token"]').getAttribute("content"),i=await fetch(o.cekOngkir(),{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":n},credentials:"same-origin",body:JSON.stringify({origin_area_id:e,destination_area_id:a,couriers:["jne","jnt","sicepat","anteraja"].join(","),items:t})}).then(l=>l.json());if(!i.success||!i.pricing)throw new Error;this.renderOngkirTable(i.pricing),this.saveOngkirDataToForm(i.pricing)}catch{document.getElementById("harga_ongkir_list").innerHTML=`
        <div class="text-danger">Gagal mengambil ongkir.</div>`,this.saveOngkirDataToForm([])}}renderOngkirTable(e=[]){const a=e.map(t=>`
      <tr>
        <td>${t.courier_name}</td>
        <td>${t.courier_service_name}</td>
        <td>${t.shipment_duration_range??"-"} hari</td>
        <td>${r(t.price)}</td>
      </tr>
    `).join("");document.getElementById("harga_ongkir_list").innerHTML=`
      <div class="table-responsive mt-2">
        <table class="table table-sm table-bordered">
          <thead class="table-light text-center">
            <tr><th>Kurir</th><th>Layanan</th><th>Estimasi</th><th>Harga</th></tr>
          </thead>
          <tbody>${a}</tbody>
        </table>
      </div>`}saveOngkirDataToForm(e=[]){const a=e.map(n=>({courier_code:n.courier_code,courier_name:n.courier_name,courier_service_name:n.courier_service_name,shipment_duration_range:n.shipment_duration_range,price:n.price})),t=document.getElementById("shippings-data");t&&(t.value=JSON.stringify(a))}fillSoHeader(e){const a=e.customer||{};document.getElementById("tanggal_so").textContent=e.tanggal||"-",document.getElementById("metode_pembayaran").textContent=e.metode_pembayaran||"-",document.getElementById("kode_customer").textContent=a.kode_customer||"-",document.getElementById("nama_toko").textContent=a.nama_toko||"-",document.getElementById("alamat").textContent=a.alamat||"-",document.getElementById("pemilik").textContent=a.pemilik||"-",document.getElementById("pasar").textContent=`(${a.id_pasar||"-"}) ${a.nama_pasar||"-"}`}fillSoDetails(e){let a=`
            <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light text-center">
                <tr>
                    <th>Produk</th><th>Kemasan</th><th>Harga</th>
                    <th>Qty</th><th>Diskon</th><th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
        `;e.details.forEach(t=>{const n=parseFloat(t.harga);a+=`
                <tr class="product-row"
                    data-product_id="${t.product_id}"
                    data-nama="${t.nama_produk}"
                    data-description="${t.kemasan}"
                    data-value="${n}"
                    data-length="${t.panjang||10}"
                    data-width="${t.lebar||10}"
                    data-height="${t.tinggi||10}"
                    data-weight="${t.berat||1e3}"
                    data-qty="${t.qty}">
                    <!-- Hidden inputs tetap disertakan -->
                    <input type="hidden"
                        name="detail[${t.product_id}][product_id]"
                        value="${t.product_id}">
                    <td>
                    ${t.kode_produk} - ${t.nama_produk}
                    </td>
                    <td>
                    ${t.kemasan}
                    </td>
                    <td class="text-end">
                    ${r(n)}
                    <input type="hidden"
                            name="detail[${t.product_id}][harga]"
                            value="${n}">
                    </td>
                    <td class="text-end">
                    ${d(t.qty)}
                    <input type="hidden"
                            name="detail[${t.product_id}][qty]"
                            value="${t.qty}">
                    </td>
                    <td class="text-end">
                    ${r(t.diskon)}
                    <input type="hidden"
                            name="detail[${t.product_id}][diskon]"
                            value="${t.diskon}">
                    </td>
                    <td class="text-end">
                    ${r(t.subtotal)}
                    <input type="hidden"
                            name="detail[${t.product_id}][subtotal]"
                            value="${t.subtotal}">
                    </td>
                </tr>
                `}),a+=`
            </tbody>
            <tfoot>
            <!-- ...tfoot sama seperti sebelumnya... -->
            </tfoot>
        </table>
        </div>
    `,this.detailWrapper.innerHTML=a}clearSoDetails(){this.detailWrapper.innerHTML=`
      <div class="text-center alert alert-warning rounded-4">
        Tidak ada detail yang ditampilkan.
      </div>`}}const h=new c;export{h as default};
