<div class="row mb-4">
    <div class="col-md-6">
        <form id="searchForm" class="d-flex">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Search courses..." name="search_term">
                <button class="btn btn-outline-dark" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>


<div id="coursesContainer" class="row">
    <?php foreach ($courses as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card course-card">
                <div class="card-body">
                    <h5 class="card-title"><?= $course['course_name'] ?></h5>
                    <p class="card-text"><?= $course['course_description'] ?></p>
                    <a href="/courses/view/<?= $course['id'] ?>" class="btn btn-primary">View Course</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script> $(document).ready(function () {

// Client-side filtering
$("#searchInput").on('keyup', function () {
    var value = $(this).val().toLowerCase();
    $('.course-card').filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

// Server-side search with AJAX
$("#searchForm").on('submit', function(e) {
    e.preventDefault();
    var searchTerm = $("#searchInput").val();

    $.get('/courses/search', { search_term: searchTerm }, function(data) {
        $("#coursesContainer").empty();

        if (data.length > 0) {
            $.each(data, function(index, course) {
                var courseHtml = `
                    <div class="col-md-4 mb-4">
                        <div class="card course-card">
                            <div class="card-body">
                                <h5 class="card-title">${course.course_name}</h5>
                                <p class="card-text">${course.course_description}</p>
                                <a href="/courses/view/${course.id}" class="btn btn-primary">View Course</a>
                            </div>
                        </div>
                    </div>
                `;
                $("#coursesContainer").append(courseHtml);
            });
        } else {
            $("#coursesContainer").html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
        }
    });
});
});
</script>