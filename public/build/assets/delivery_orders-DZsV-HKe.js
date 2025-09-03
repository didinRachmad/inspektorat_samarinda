import{f as i,a as d}from"./format-B7a5NblC.js";class c{constructor(){this.datatableEl=$("#datatables"),this.soSelectEl=$("#selectSalesOrder"),this.detailWrapper=document.getElementById("so-details-wrapper"),this.areaSelectEls=$("#origin, #destination"),this.btnCekOngkir=document.getElementById("btnCekOngkir"),this.soUrl=$("#so-select-wrapper").data("get-sales-order-url"),this.soDetailTpl=$("#so-select-wrapper").data("get-sales-order-detail-url"),this.areaUrl=$("#area-wrapper").data("area-url"),this.cekOngkirUrl=$("#area-wrapper").data("cek-ongkir-url")}initIndex(){console.log("Halaman Transaksi DO Index berhasil dimuat!"),this.initDataTable()}initShow(){console.log("Halaman Transaksi DO Show berhasil dimuat!")}initCreate(){console.log("Halaman Transaksi DO Create berhasil dimuat!"),this.initForm()}initEdit(){console.log("Halaman Transaksi DO Edit berhasil dimuat!"),this.initForm()}initDataTable(){const e=this.datatableEl.data("url");this.datatableEl.DataTable({processing:!0,serverSide:!0,ajax:e,columns:[{data:"DT_RowIndex",name:"DT_RowIndex",className:"text-center",orderable:!1,searchable:!1},{data:"no_do",name:"no_do",className:"text-center"},{data:"no_so",name:"no_so",className:"text-center"},{data:"tanggal",name:"tanggal",className:"text-center"},{data:"customer",name:"customer"},{data:"metode_pembayaran",name:"metode_pembayaran",className:"text-center"},{data:"total_qty",name:"total_qty",className:"text-end"},{data:"total_diskon",name:"total_diskon",className:"text-end",render:$.fn.dataTable.render.number(".",",",0,"Rp ")},{data:"grand_total",name:"grand_total",className:"text-end fw-bold",render:$.fn.dataTable.render.number(".",",",0,"Rp ")},{data:"approval_level",name:"approval_level"},{data:"status",name:"status"},{data:"keterangan",name:"keterangan"},{data:null,orderable:!1,searchable:!1,className:"text-center no-export",render:function(n,s,t){let r="";return t.can_show&&(r+=`
                            <a href="${t.show_url}" class="btn btn-sm rounded-4 btn-info" data-bs-toggle="tooltip" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        `),t.can_approve&&(t.approval_level==0?r+=`
                                    <form action="${t.approve_url}" method="POST" class="d-inline form-approval">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                        <button type="submit" class="btn btn-sm rounded-4 btn-success btn-approve" data-bs-toggle="tooltip" title="Ajukan">
                                            <i class="bi bi-check2-square"></i>
                                        </button>
                                    </form>
                                `:r+=`
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
                                `),t.can_modify&&(t.can_edit&&(r+=`
                                    <a href="${t.edit_url}" class="btn btn-sm rounded-4 btn-warning" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                `),t.can_delete&&(r+=`
                                    <form action="${t.delete_url}" method="POST" class="d-inline form-delete">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-sm rounded-4 btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                `)),`<div class="d-flex justify-content-center gap-1 flex-wrap">${r}</div>`}}],dom:"<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-6 mb-3 mb-md-0 d-flex justify-content-center align-items-center'><'col-sm-12 col-md-3 text-right'f>><'row py-2'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",paging:!0,responsive:!0,pageLength:20,lengthMenu:[[20,50,-1],[20,50,"Semua"]],order:[],columnDefs:[{targets:0,className:"text-center"}],info:!0,language:{sEmptyTable:"Tidak ada data yang tersedia di tabel",sInfo:"Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",sInfoEmpty:"Menampilkan 0 hingga 0 dari 0 entri",sInfoFiltered:"(disaring dari _MAX_ entri keseluruhan)",sInfoPostFix:"",sInfoThousands:".",sLengthMenu:"Tampilkan _MENU_ entri",sLoadingRecords:"Memuat...",sProcessing:"Sedang memproses...",sSearch:"Cari:",sZeroRecords:"Tidak ditemukan data yang cocok",oAria:{sSortAscending:": aktifkan untuk mengurutkan kolom secara menaik",sSortDescending:": aktifkan untuk mengurutkan kolom secara menurun"}},drawCallback:function(){$('[data-bs-toggle="tooltip"]').each(function(){new bootstrap.Tooltip(this)})}})}initForm(){this.setupSoSelect(),this.setupAreaSelect(),this.btnCekOngkir.addEventListener("click",()=>this.checkOngkir());const e=document.getElementById("shipping-init-data");if(e)try{const n=JSON.parse(e.dataset.shippings);this.renderOngkirTable(n),this.saveOngkirDataToForm(n)}catch(n){console.error("Gagal memuat data ongkir dari server",n)}}setupSoSelect(){this.soSelectEl,this.soSelectEl.select2({theme:"bootstrap-5",placeholder:"Pilih Sales Order…",allowClear:!0,ajax:{url:this.soUrl,dataType:"json",delay:250,data:e=>({q:e.term,page:e.page||1}),processResults:(e,n)=>({results:e.results,pagination:{more:e.pagination.more}}),cache:!0},templateResult:({loading:e,no_so:n,tanggal:s,kode_customer:t,nama_toko:r})=>e?"Mencari…":$(`
                        <div>
                        <strong>${n}</strong> – ${s}<br>
                        <small class="text-muted">${t} | ${r}</small>
                        </div>
                    `),templateSelection:e=>e.no_so||e.text}).on("select2:select",async e=>{const n=e.params.data.id;if(!n)return;const s=this.soDetailTpl.replace("__ID__",n);let t;try{t=await fetch(s).then(a=>a.json())}catch(a){console.error("Gagal load detail SO:",a);return}const r=t.customer||{};$("#tanggal_so").text(t.tanggal||"-"),$("#metode_pembayaran").text(t.metode_pembayaran||"-"),$("#kode_customer").text(r.kode_customer||"-"),$("#nama_toko").text(r.nama_toko||"-"),$("#alamat").text(r.alamat||"-"),$("#pemilik").text(r.pemilik||"-"),$("#pasar").text(`(${r.id_pasar||"-"}) ${r.nama_pasar||"-"}`);let o=`
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                        <thead class="table-light text-center">
                            <tr>
                            <th>Produk</th><th>Kemasan</th><th>Harga</th>
                            <th>Qty</th><th>Diskon</th><th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;t.details.forEach((a,u)=>{const l=parseFloat(a.harga);o+=`
                        <tr class="product-row"
                        data-product_id="${a.product_id}"
                        data-nama="${a.nama_produk}"
                        data-description="${a.kemasan}"
                        data-value="${l}"
                        data-length="${a.panjang||10}"
                        data-width="${a.lebar||10}"
                        data-height="${a.tinggi||10}"
                        data-weight="${a.berat||1e3}"
                        data-qty="${a.qty}">
                            <input type="hidden" name="detail[${a.product_id}][product_id]" value="${a.product_id}">
                            <td>
                            ${a.kode_produk} - ${a.nama_produk}
                            </td>
                            <td>
                            ${a.kemasan}
                            </td>
                            <td class="text-end">
                            ${i(l)}
                            <input type="hidden" name="detail[${a.product_id}][harga]" value="${l}">
                            </td>
                            <td class="text-end">
                            ${d(a.qty)}
                            <input type="hidden" name="detail[${a.product_id}][qty]" value="${a.qty}">
                            </td>
                            <td class="text-end">
                            ${i(a.diskon)}
                            <input type="hidden" name="detail[${a.product_id}][diskon]" value="${a.diskon}">
                            </td>
                            <td class="text-end">
                            ${i(a.subtotal)}
                            <input type="hidden" name="detail[${a.product_id}][subtotal]" value="${a.subtotal}">
                            </td>
                        </tr>
                        `}),o+=`
                    </tbody>
                    <tfoot>
                        <tr>
                        <td colspan="5" class="text-end fw-semibold">QTY Total</td>
                        <td class="text-end">
                            ${d(t.total_qty)}
                        </td>
                        </tr>
                        <tr>
                        <td colspan="5" class="text-end fw-semibold">Diskon Total</td>
                        <td class="text-end">
                            ${i(t.total_diskon)}
                        </td>
                        </tr>
                        <tr>
                        <td colspan="5" class="text-end fw-bold">Grand Total</td>
                        <td class="text-end fw-bold">
                            ${i(t.grand_total)}
                        </td>
                        </tr>
                    </tfoot>
                    </table>
                </div>
                `,this.detailWrapper.innerHTML=o}).on("select2:clear",()=>{$("#tanggal_so").text("-"),$("#metode_pembayaran").text("-"),$("#kode_customer").text("-"),$("#nama_toko").text("-"),$("#alamat").text("-"),$("#pemilik").text("-"),$("#pasar").text("-"),this.detailWrapper.innerHTML=`
                    <div class="text-center alert alert-warning rounded-4">
                        Tidak ada detail yang ditampilkan.
                    </div>
                `})}setupAreaSelect(){this.areaSelectEls.select2({theme:"bootstrap-5",placeholder:"Cari wilayah…",allowClear:!0,minimumInputLength:3,ajax:{url:this.areaUrl,dataType:"json",delay:300,data:e=>({q:e.term}),processResults:e=>({results:e.areas.map(n=>({id:n.id,text:n.name}))}),cache:!0}}),this.areaSelectEls.on("select2:select",function(e){const n=$(this).attr("id"),s=e.params.data.text;$(`#${n}_name`).val(s)}),this.areaSelectEls.on("select2:clear",function(){const e=$(this).attr("id");$(`#${e}_name`).val("")})}async checkOngkir(){const e=this.areaSelectEls.filter("#origin").val(),n=this.areaSelectEls.filter("#destination").val(),s=[...document.querySelectorAll(".product-row")].map(t=>({name:t.dataset.nama,description:t.dataset.description,value:+t.dataset.value,length:+t.dataset.length,width:+t.dataset.width,height:+t.dataset.height,weight:+t.dataset.weight,quantity:+t.dataset.qty})).filter(t=>t.quantity>0);if(!e||!n||s.length===0)return showToast(!e||!n?"Silakan pilih asal & tujuan pengiriman dulu.":"Tidak ada item valid untuk menghitung ongkir.","info");$("#harga_ongkir_list").html(`<div class="d-flex justify-content-center py-4">
            <div class="spinner-border" role="status"></div>
            <span class="ms-2">Loading ongkir…</span>
        </div>`);try{const t=await fetch(this.cekOngkirUrl,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},credentials:"same-origin",body:JSON.stringify({origin_area_id:e,destination_area_id:n,couriers:["jne","jnt","sicepat","anteraja"].join(","),items:s})}).then(r=>r.json());if(!t.success||!t.pricing)throw new Error;this.renderOngkirTable(t.pricing),this.saveOngkirDataToForm(t.pricing)}catch{$("#harga_ongkir_list").html('<div class="text-danger">Gagal mengambil ongkir.</div>'),this.saveOngkirDataToForm([])}}renderOngkirTable(e=[]){const n=e.map(s=>`
      <tr>
        <td>${s.courier_name}</td>
        <td>${s.courier_service_name}</td>
        <td>${s.shipment_duration_range??"-"} hari</td>
        <td>${i(s.price)}</td>
      </tr>`).join("");$("#harga_ongkir_list").html(`
      <div class="table-responsive mt-2">
        <table class="table table-sm table-bordered">
          <thead class="table-light text-center">
            <tr><th>Kurir</th><th>Layanan</th><th>Estimasi</th><th>Harga</th></tr>
          </thead>
          <tbody>${n}</tbody>
        </table>
      </div>`)}saveOngkirDataToForm(e=[]){const n=e.map(t=>({courier_code:t.courier_code,courier_name:t.courier_name,courier_service_name:t.courier_service_name,shipment_duration_range:t.shipment_duration_range,price:t.price})),s=document.querySelector("#shippings-data");s&&(s.value=JSON.stringify(n))}}const h=new c;export{h as default};
