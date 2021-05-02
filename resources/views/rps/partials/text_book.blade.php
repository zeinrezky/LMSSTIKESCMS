<div class="row">
    <div class="col-md-12">
        <h3>Text Book</h3>
        <hr style = "color:grey">
        <div class="col-md-4 text-center">
            <img width = "200" src="{{ Storage::url(contents_path().$model->gbr_cover) }}" alt="">
        </div>
        <div class="col-md-8">
            <table class= "table">
                <tbody>
                    <tr>
                        <td class = "active">Judul</td>
                        <td>{{ $model->title }}</td>
                        
                        <td class = "active">Edisi</td>
                        <td>{{ $model->edition }}</td>
                    </tr>
                    <tr>
                        <td class = "active">Pengarang</td>
                        <td>{{ $model->author }}</td>
                        
                        <td class = "active">Penerbit</td>
                        <td>{{ $model->publisher }}</td>
                    </tr>
                    <tr>
                        <td class = "active">Tahun</td>
                        <td>{{ $model->tahun }}</td>
                        
                        <td class = "active">Kategori</td>
                        <td>{{ $model->kategori }}</td>
                    </tr>
                    <tr>
                        <td class = "active">ISBN</td>
                        <td>{{ $model->isbn }}</td>
                    </tr>
                </tbody>
            </table>   
        </div>
    </div>
</div>