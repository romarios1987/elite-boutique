</div> <!--/.container-fluid  content-->
<footer class="text-center" id="footer">&copy; Copyright <?= date('Y') ?> Elite Boutique</footer>

</div><!--wrapper-->

<script>
    function updateSizes() {
        var sizeString = '';
        for (var i = 1; i <= 12; i++){
            if($('#size' + i).val() != ''){
                sizeString += $('#size' + i).val()+':'+$('#quantity' + i).val()+','
            }
        }
    $('#sizes').val(sizeString);
    }

    /** ---------------------------***/
    function getChildOption(selected) {
        var parentID = $('#parent').val();
        if (typeof selected == 'undefined'){
            var selected = '';
        }
        $.ajax({
            url: '/admin/parsers/child_categories.php',
            type: 'POST',
            data: {parentID: parentID, selected: selected},
            success: function (data) {
                $('#child').html(data);
            },
            error: function () {
                alert("Error")
            },
        });
    }
    $('select[name="parent"]').change(function () {
        getChildOption();
    });
</script>

</body>
</html>