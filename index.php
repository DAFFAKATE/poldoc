<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$userRole = getUserRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelolaan Dokumen Politeknik Jambi</title>
    
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/color.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/demo/demo.css">
    
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Menghindari scroll */
        }
        .layout-panel-west .panel-header {
            background-color: #f0f0f0;
        }
        .layout-panel-west .panel-body {
            background-color: #fafafa;
        }
        #documentTree li {
            padding: 5px 0;
        }
        .toolbar {
            padding: 5px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }
        .search-container {
            float: right;
        }
        #searchBar {
            width: 200px;
            padding: 5px;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 9999;
        }
        #preview-dialog {
            width: 80%;
            height: 80%;
            padding: 10px;
        }
        #preview-frame {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <div class="easyui-layout" style="width:100%; height:100%;">
        <div data-options="region:'north'" style="height:50px; padding:10px;">
            <h2 style="margin:0;">Pengelolaan Dokumen Politeknik Jambi</h2>
            <a href="login.php" class="easyui-linkbutton logout-btn" iconCls="icon-cancel">Logout</a>
        </div>
        
        <div data-options="region:'west',split:true" title="Dashboard" style="width:200px;">
            <ul class="easyui-tree" id="documentTree" style="margin: 10px 0 0; padding: 0; width: 100%;">
                <li><span>Dokumen</span></li>
                <?php if ($userRole === 'Admin' || $userRole === 'KETUA LP3M'): ?>
                <li><span>User</span></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div data-options="region:'center',title:'Tabel'" style="padding:5px;">
            <div class="toolbar" id="toolbar">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newItem()">Tambah</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editItem()">Edit</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyItem()">Hapus</a>
                
                <div class="search-container">
                    <input id="searchBar" class="easyui-searchbox" data-options="prompt:'Search...',searcher:searchItems">
                </div>
            </div>
            
            <!-- Tabel Dokumen -->
            <table id="dg-documents" class="easyui-datagrid" style="width:100%;" 
                   url="get_documents.php"
                   toolbar="#toolbar" pagination="true"
                   rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                    <tr>
                        <th field="year" width="50" sortable="true">Tahun</th>
                        <th field="title" width="100" sortable="true">Judul</th>
                        <th field="creator" width="100" sortable="true">Pembuat</th>
                        <th field="description" width="150">Deskripsi</th>
                        <th field="upload_date" width="100" sortable="true">Tanggal Upload</th>
                        <th field="category" width="80" sortable="true">Kategori</th>
                        <th field="status" width="80" sortable="true">Status</th>
                        <th field="file_path" width="100" formatter="formatPreviewButton">Preview</th>
                    </tr>
                </thead>
            </table>

            <!-- Tabel User -->
            <table id="dg-users" class="easyui-datagrid" style="width:100%; display:none;" 
                   url="get_users.php"
                   toolbar="#toolbar" pagination="true"
                   rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                    <tr>
                        <th field="name" width="100" sortable="true">Nama</th>
                        <th field="email" width="150" sortable="true">Email</th>
                        <th field="nidn" width="100" sortable="true">NIDN</th>
                        <th field="username" width="100" sortable="true">Username</th>
                        <th field="role" width="80" sortable="true">Role</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Dialog untuk Dokumen -->
    <div id="dlg-document" class="easyui-dialog" style="width:400px;" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-document'">
        <form id="fm-document" method="post" enctype="multipart/form-data" novalidate style="margin:0;padding:20px;">
            <h3>Informasi Dokumen</h3>
            <input type="hidden" name="id">
            <div style="margin-bottom:10px">
                <input name="title" class="easyui-textbox" required="true" label="Judul:" style="width:100%">
            </div>
            <div style="margin-bottom:10px">
                <input name="description" class="easyui-textbox" required="true" label="Deskripsi:" multiline="true" style="width:100%;height:60px">
            </div>
            <div style="margin-bottom:10px">
                <select name="category" class="easyui-combobox" label="Kategori:" style="width:100%">
                    <option value="Kategori 1">Kategori 1</option>
                    <option value="Kategori 2">Kategori 2</option>
                    <option value="Kategori 3">Kategori 3</option>
                </select>
            </div>
            <div style="margin-bottom:10px">
                <input name="fileUpload" class="easyui-filebox" label="File:" style="width:100%">
            </div>
            <?php if ($userRole === 'KETUA LP3M'): ?>
            <div style="margin-bottom:10px">
                <select name="status" class="easyui-combobox" label="Status:" style="width:100%">
                    <option value="Draft">Draft</option>
                    <option value="Approve">Approve</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            <?php endif; ?>
        </form>
    </div>
    <div id="dlg-buttons-document">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDocument()" style="width:90px">Simpan</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-document').dialog('close')" style="width:90px">Batal</a>
    </div>

    <!-- Dialog untuk User -->
    <div id="dlg-user" class="easyui-dialog" style="width:400px;" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-user'">
        <form id="fm-user" method="post" novalidate style="margin:0;padding:20px;">
            <h3>Informasi User</h3>
            <input type="hidden" name="id">
            <div style="margin-bottom:10px">
                <input name="name" class="easyui-textbox" required="true" label="Nama:" style="width:100%">
            </div>
            <div style="margin-bottom:10px">
                <input name="email" class="easyui-textbox" required="true" validType="email" label="Email:" style="width:100%">
            </div>
            <div style="margin-bottom:10px">
                <input name="birth_date" class="easyui-datebox" required="true" label="Tanggal Lahir:" style="width:100%">
            </div>
            <div style="margin-bottom:10px">
                <input name="nidn" class="easyui-textbox" label="NIDN:" style="width:100%">
            </div>
            <div style="margin-bottom:10px">
                <input name="username" class="easyui-textbox" required="true" label="Username:" style="width:100%">
            </div>
            <div style="margin-bottom:10px">
                <input name="password" class="easyui-passwordbox" label="Password:" style="width:100%">
            </div>
            <div style="margin-bottom:10px">
                <select name="role" class="easyui-combobox" label="Role:" style="width:100%">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                    <?php if ($userRole === 'KETUA LP3M'): ?>
                    <option value="KETUA LP3M">KETUA LP3M</option>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>
    <div id="dlg-buttons-user">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser()" style="width:90px">Simpan</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-user').dialog('close')" style="width:90px">Batal</a>
    </div>

    <!-- Preview Dialog -->
    <div id="preview-dialog" class="easyui-dialog" closed="true" modal="true">
        <iframe id="preview-frame" src=""></iframe>
    </div>

    <script type="text/javascript">
        var url;
        var currentTable = 'documents';

        function newItem(){
            var dlg = currentTable === 'documents' ? '#dlg-document' : '#dlg-user';
            var fm = currentTable === 'documents' ? '#fm-document' : '#fm-user';
            $(dlg).dialog('open').dialog('center').dialog('setTitle','Tambah ' + (currentTable === 'documents' ? 'Dokumen' : 'User'));
            $(fm).form('clear');
            url = currentTable === 'documents' ? 'save_document.php' : 'save_user.php';
        }

        function editItem(){
            var row = $('#dg-' + currentTable).datagrid('getSelected');
            if (row){
                var dlg = currentTable === 'documents' ? '#dlg-document' : '#dlg-user';
                var fm = currentTable === 'documents' ? '#fm-document' : '#fm-user';
                $(dlg).dialog('open').dialog('center').dialog('setTitle','Edit ' + (currentTable === 'documents' ? 'Dokumen' : 'User'));
                $(fm).form('load',row);
                url = (currentTable === 'documents' ? 'save_document.php' : 'save_user.php') + '?id='+row.id;
            } else {
                $.messager.alert('Warning', 'Please select an item to edit.', 'warning');
            }
        }

        function saveDocument(){
            $('#fm-document').form('submit',{
                url: url,
                iframe: false,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
                    var result = eval('('+result+')');
                    if (result.errorMsg){
                        $.messager.show({
                            title: 'Error',
                            msg: result.errorMsg
                        });
                    } else {
                        $('#dlg-document').dialog('close');
                        $('#dg-documents').datagrid('reload');
                    }
                }
            });
        }

        function saveUser(){
            $('#fm-user').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
                    var result = eval('('+result+')');
                    if (result.errorMsg){
                        $.messager.show({
                            title: 'Error',
                            msg: result.errorMsg
                        });
                    } else {
                        $('#dlg-user').dialog('close');
                        $('#dg-users').datagrid('reload');
                    }
                }
            });
        }

        function destroyItem(){
            var row = $('#dg-' + currentTable).datagrid('getSelected');
            if (row){
                $.messager.confirm('Confirm','Are you sure you want to delete this item?',function(r){
                    if (r){
                        $.post(currentTable === 'documents' ? 'delete_document.php' : 'delete_user.php',{id:row.id},function(result){
                            if (result.success){
                                $('#dg-' + currentTable).datagrid('reload');
                            } else {
                                $.messager.show({
                                    title: 'Error',
                                    msg: result.errorMsg
                                });
                            }
                        },'json');
                    }
                });
            } else {
                $.messager.alert('Warning', 'Please select an item to delete.', 'warning');
            }
        }

        function searchItems(value) {
            $('#dg-' + currentTable).datagrid('load', {
                search: value
            });
        }

        $('#documentTree').tree({
            onClick: function(node){
                if(node.text === 'Dokumen'){
                    $('#dg-documents').show();
                    $('#dg-users').hide();
                    currentTable = 'documents';
                } else if(node.text === 'User'){
                    $('#dg-documents').hide();
                    $('#dg-users').show();
                    currentTable = 'users';
                }
                // Refresh toolbar untuk memperbarui status tombol
                $('#toolbar').find('a.easyui-linkbutton').linkbutton();
            }
        });

        function formatPreviewButton(value, row, index) {
            if (value) {
                return '<a href="javascript:void(0)" onclick="previewDocument(\'' + value + '\')" class="easyui-linkbutton" iconCls="icon-search">View</a>';
            }
            return '';
        }

        function previewDocument(filePath) {
            $('#preview-frame').attr('src', filePath);
            $('#preview-dialog').dialog({
                width: 1000, // Sesuaikan ukuran lebar sesuai kebutuhan
                height: 600, // Sesuaikan ukuran tinggi sesuai kebutuhan
                modal: true,
                title: 'Preview File'
            }).dialog('open').dialog('center');
        }
        // Initialize the layout
        $('body').layout('resize');
    </script>
</body>
</html>
