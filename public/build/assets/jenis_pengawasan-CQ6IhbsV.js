import{r as i}from"./routes-De8qlvmc.js";const l={datatable:()=>i("jenis_pengawasan.data")};class s{constructor(){this.pageName="Master Jenis Pengawasan",this.datatableEl=$("#datatables"),this.childIndex=$("#child-items .child-item").length||0}initIndex(){console.log(`Halaman ${this.pageName} Index berhasil dimuat!`),this.initDataTable()}initShow(){console.log(`Halaman ${this.pageName} Show berhasil dimuat!`)}initCreate(){console.log(`Halaman ${this.pageName} Create berhasil dimuat!`),this.initForm()}initEdit(){console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`),this.initForm()}initDataTable(){this.datatableEl.DataTable({processing:!0,serverSide:!0,ajax:l.datatable(),columns:[{data:"DT_RowIndex",name:"DT_RowIndex"},{data:"nama",name:"nama"},{data:"urutan",name:"urutan"},{data:null,orderable:!1,searchable:!1,className:"text-center no-export",render:function(e,n,a){let t="";return a.can_edit&&(t+=`
                                <a href="${a.edit_url}" class="btn btn-sm btn-warning rounded-4" data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            `),a.can_delete&&(t+=`
                                <form action="${a.delete_url}" method="POST" class="d-inline form-delete">
                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="btn btn-sm btn-danger rounded-4 btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            `),`<div class="d-flex justify-content-center gap-1">${t}</div>`}}],dom:"<'row'<'col-md-3'l><'col-md-6 text-center'><'col-md-3'f>><'row table-responsive py-2'<'col-sm-12'tr>><'row'<'col-md-5'i><'col-md-7'p>>",paging:!0,responsive:!0,pageLength:20,lengthMenu:[[20,50,-1],[20,50,"Semua"]],order:[[2,"asc"]],info:!0,language:{sEmptyTable:"Tidak ada data yang tersedia di tabel",sInfo:"Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",sInfoEmpty:"Menampilkan 0 hingga 0 dari 0 entri",sInfoFiltered:"(disaring dari _MAX_ entri keseluruhan)",sLengthMenu:"Tampilkan _MENU_ entri",sLoadingRecords:"Memuat...",sProcessing:"Sedang memproses...",sSearch:"Cari:",sZeroRecords:"Tidak ditemukan data yang cocok",oAria:{sSortAscending:": aktifkan untuk mengurutkan kolom menaik",sSortDescending:": aktifkan untuk mengurutkan kolom menurun"}},drawCallback:()=>$('[data-bs-toggle="tooltip"]').each(function(){new bootstrap.Tooltip(this)})})}initForm(){const e=this;$("#add-child").on("click",function(){let n=`
            <div class="row mb-2 child-item">
                <div class="col-md-6">
                    <input type="text" name="children[${e.childIndex}][nama]" class="form-control form-control-sm" placeholder="Nama Sub Item" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="children[${e.childIndex}][urutan]" class="form-control form-control-sm" placeholder="Urutan" value="1" min="1" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-child">Hapus <i class="bi bi-trash-fill"></i></button>
                </div>
            </div>`;$("#child-items").append(n),e.childIndex++}),$(document).on("click",".remove-child",function(){$(this).closest(".child-item").remove()})}}const r=new s;export{r as default};
