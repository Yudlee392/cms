$(document).ready(function() {
    loadPosts();
    loadModules();

    function loadPosts(moduleIds = []) {
        $.ajax({
            url: 'fetch_posts.php',
            type: 'GET',
            data: {moduleIds: moduleIds},
            success: function(data) {
                $('#posts').html(data);
            }
        });
    }

    function loadModules() {
        $.ajax({
            url: 'fetch_modules.php',
            type: 'GET',
            success: function(data) {
                $('#modules').html(data);
                $('#modules input[type="checkbox"]').change(function() {
                    let selectedModules = [];
                    $('#modules input:checked').each(function() {
                        selectedModules.push($(this).val());
                    });
                    loadPosts(selectedModules);
                });
            }
        });
    }
});
