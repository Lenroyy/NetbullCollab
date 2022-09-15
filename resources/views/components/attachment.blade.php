<input type="file" id="files" name="files[]" multiple>
&nbsp;
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Size</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $x = 0;
                    ?>
                    @foreach($files as $file)
                        <?php
                            $x++;
                        ?>
                        <tr style="cursor: pointer;">
                            <td><a href="/storage/{{ $file->filename }}" target="<?= $x; ?>">{{ $file->original_name }}</a></td>
                            <td>{{ $file->file_size }} Kb</td>
                            <td>
                                <div class="btn-group m-r-10 pull-right">
                                    <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button"><span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li><a target="<?= $x; ?>" href="/storage/{{ $file->filename }}">View</a></li>
                                        <li><a href="/deleteFile/{{ $file->id }}">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>&nbsp;
            <br>&nbsp;
            <br>&nbsp;
        </div>
    </div>
</div>