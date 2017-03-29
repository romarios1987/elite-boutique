</div><!--/.row-->
</div><!--wrapper-->
<footer class="text-center" id="footer">&copy; Copyright <?= date('Y') ?> Elite Boutique</footer>


<script>
    /*** detailsModal ***/
    function detailsModal(id) {
        var data = {"id": id};
        $.ajax({
            url: "includes/details-modal.php",
            method: "post",
            data: data,
            success: function (data) {
                $('body').append(data);
                $('#details-modal').modal('toggle');
            },
            error: function () {
                alert('Ошибка!!!');
            }
        });
    }


    /***  Update Cart   **/
    function update_cart(mode, edit_id, edit_size) {
        var data = {"mode": mode, "edit_id": edit_id, "edit_size": edit_size};
        $.ajax({
            url: '/admin/parsers/update_cart.php',
            method: 'post',
            data: data,
            success: function () {
                location.reload();
            },
            error: function () {
                alert("Что то не так!");
            }
        });
    }


    /***  add_to_cart   **/
    function add_to_cart() {
        $('#modal_errors').html("");
        var sizes = $('#size').val();
        var quantity = $('#quantity').val();
        var available = $('#available').val();
        var error = '';
        var data = $('#add_product_form').serialize();
        if (size == '' || quantity == '' || quantity == 0) {
            error += '<p class="text-danger text-center">You must choose a size and quantity!</p>';
            $('#modal_errors').html(error);
            return;
        } else if (quantity > available) {
            error += '<p class="text-danger text-center">There are only ' + available + ' available !</p>';
            $('#modal_errors').html(error);
            return;

        } else {
            $.ajax({
                url: '/admin/parsers/add_cart.php',
                method: 'post',
                data: data,
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert("Что то не так!");
                }
            });
        }


    }

</script>

</body>
</html>