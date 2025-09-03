import{A as o}from"./dashboard-BRhlnPRH.js";import{f as u}from"./format-B7a5NblC.js";import"./vendor-DcHDpzIg.js";class m{constructor(){this.datatableEl=$("#datatables"),this.customerSelectEl=$("#selectCustomer"),this.customerUrl=$("#customer-select-wrapper").data("get-customers-url")}initIndex(){console.log("Halaman Transaksi SO Index berhasil dimuat!"),this.initDataTable()}initShow(){console.log("Halaman Transaksi SO Show berhasil dimuat!")}initCreate(){console.log("Halaman Transaksi SO Create berhasil dimuat!"),this.initForm()}initEdit(){console.log("Halaman Transaksi SO Edit berhasil dimuat!"),this.initForm()}initDataTable(){this.datatableEl.DataTable({processing:!0,serverSide:!0,ajax:this.datatableEl.data("url"),columns:[{data:"DT_RowIndex",name:"DT_RowIndex",className:"text-center",orderable:!1,searchable:!1},{data:"no_so",name:"no_so",className:"text-center"},{data:"tanggal",name:"tanggal"},{data:"customer",name:"customer"},{data:"metode_pembayaran",name:"metode_pembayaran",className:"text-center"},{data:"total_qty",name:"total_qty",className:"text-end",render:$.fn.dataTable.render.number(".",",",0)},{data:"total_diskon",name:"total_diskon",className:"text-end",render:$.fn.dataTable.render.number(".",",",0,"Rp ")},{data:"grand_total",name:"grand_total",className:"text-end fw-bold",render:$.fn.dataTable.render.number(".",",",0,"Rp ")},{data:"approval_level",name:"approval_level"},{data:"status",name:"status"},{data:"keterangan",name:"keterangan"},{data:null,orderable:!1,searchable:!1,className:"text-center no-export",render:function(a,n,t){let e="";return t.can_show&&(e+=`
                                <a href="${t.show_url}" class="btn btn-sm rounded-4 btn-info" data-bs-toggle="tooltip" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            `),t.can_approve&&(t.approval_level==0?e+=`
                                    <form action="${t.approve_url}" method="POST" class="d-inline form-approval">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                        <button type="submit" class="btn btn-sm rounded-4 btn-success btn-approve" data-bs-toggle="tooltip" title="Ajukan">
                                            <i class="bi bi-check2-square"></i>
                                        </button>
                                    </form>
                                `:e+=`
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
                                `),t.can_modify&&(t.can_edit&&(e+=`
                                    <a href="${t.edit_url}" class="btn btn-sm rounded-4 btn-warning" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                `),t.can_delete&&(e+=`
                                    <form action="${t.delete_url}" method="POST" class="d-inline form-delete">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-sm rounded-4 btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                `)),`<div class="d-flex justify-content-center gap-1 flex-wrap">${e}</div>`}}],dom:"<'row'<'col-md-3'l><'col-md-6 text-center'><'col-md-3'f>><'row py-2'<'col-sm-12'tr>><'row'<'col-md-5'i><'col-md-7'p>>",paging:!0,responsive:!0,pageLength:20,lengthMenu:[[20,50,-1],[20,50,"Semua"]],order:[],info:!0,language:{sEmptyTable:"Tidak ada data yang tersedia di tabel",sInfo:"Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",sInfoFiltered:"(disaring dari _MAX_ entri keseluruhan)",sLengthMenu:"Tampilkan _MENU_ entri",sLoadingRecords:"Memuat...",sProcessing:"Sedang memproses...",sSearch:"Cari:",sZeroRecords:"Tidak ditemukan data yang cocok",oAria:{sSortAscending:": aktifkan untuk mengurutkan kolom menaik",sSortDescending:": aktifkan untuk mengurutkan kolom menurun"}},drawCallback:()=>$('[data-bs-toggle="tooltip"]').each(function(){new bootstrap.Tooltip(this)})})}initForm(){this.setupCustomerSelect(),this.initAutoNumeric(),this.attachQtyListeners(),this.calculateAll()}setupCustomerSelect(){this.customerSelectEl.select2({theme:"bootstrap-5",placeholder:"Pilih customer…",allowClear:!0,ajax:{url:this.customerUrl,dataType:"json",delay:250,data:a=>({q:a.term,page:a.page||1}),processResults:(a,n)=>{var t;return{results:a.results.map(e=>({id:e.id,kode_customer:e.kode_customer,nama_toko:e.nama_toko,id_pasar:e.id_pasar,nama_pasar:e.nama_pasar,text:`${e.kode_customer} – ${e.nama_toko}`})),pagination:{more:((t=a.pagination)==null?void 0:t.more)||!1}}},cache:!0},templateResult:({loading:a,kode_customer:n,nama_toko:t,id_pasar:e,nama_pasar:s})=>a?"Mencari…":$(`<div>
                        <strong>${n}</strong> – ${t}<br>
                        <small class="text-muted">Pasar: ${e} – ${s}</small>
                    </div>`),templateSelection:a=>a.kode_customer?`${a.kode_customer} – ${a.nama_toko}`:a.text||"Pilih customer…"})}initAutoNumeric(){document.querySelectorAll(".diskon-input, .subtotal-input").forEach(a=>{new o(a,{digitGroupSeparator:".",decimalCharacter:",",decimalPlaces:0})})}calculateAll(){let a=0,n=0,t=0;document.querySelectorAll(".product-row").forEach(e=>{var c;const s=parseFloat(e.dataset.harga),l=((c=o.getAutoNumericElement(e.querySelector(".qty-input")))==null?void 0:c.getNumber())??0,i=s*l,r=l>=20?i*.05:0,d=i-r;a+=l,n+=r,t+=d,o.getAutoNumericElement(e.querySelector(".diskon-input")).set(r),o.getAutoNumericElement(e.querySelector(".subtotal-input")).set(d)}),$("#total-qty").text(a),$("#total-diskon").text(`Rp ${u(n)}`),$("#grand-total").text(`Rp ${u(t)}`)}attachQtyListeners(){document.querySelectorAll(".qty-input").forEach(a=>{a.addEventListener("input",()=>this.calculateAll())})}}const f=new m;export{f as default};
