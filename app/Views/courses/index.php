<?php
include('app/Views/templates/header.php');
?>

<div class="container my-5">
  <h4 class="mb-3">Courses</h4>

  <div class="row mb-3">
    <div class="col-md-6">
      <form id="courseSearchForm" class="d-flex">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" id="courseSearchInput" name="search_term" class="form-control" placeholder="Search courses...">
          <button class="btn btn-outline-dark" type="submit"><i class="bi bi-search"></i> Search</button>
        </div>
      </form>
    </div>
  </div>

  <div id="coursesContainer" class="row">
    <?php if (!empty($courses)): ?>
      <?php foreach ($courses as $course): ?>
        <div class="col-md-4 mb-4 course-card" data-search="<?= esc(strtolower((string) ($course['title'] . ' ' . $course['description'] . ' ' . ($course['semester'] ?? '') . ' ' . ($course['school_year'] ?? '')))) ?>">
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= esc($course['title']) ?></h5>
              <p class="card-text">Course Code: <?= esc($course['description']) ?></p>
              <small class="text-muted d-block">Semester/Term: <?= esc($course['semester'] ?? 'Not set') ?></small>
              <small class="text-muted d-block">SY: <?= esc($course['school_year'] ?? 'TBD') ?></small>
              <div class="mt-auto pt-2">
                <a href="<?= base_url('student/course/' . (int) ($course['id'] ?? 0)) ?>" class="btn btn-dark w-100">View Course</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12 text-muted">No courses available.</div>
    <?php endif; ?>
  </div>
</div>

<script>
  (function() {
    const searchInput = document.getElementById('courseSearchInput');
    const searchForm = document.getElementById('courseSearchForm');
    const container = document.getElementById('coursesContainer');
    const initialCourses = <?= json_encode($courses ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    let serverCourses = Array.isArray(initialCourses) ? initialCourses : [];

    const normalize = (value) => (value ?? '').toString().toLowerCase();
    const matchesTerm = (course, term) => {
      const haystack = [
        course.title,
        course.description,
        course.semester,
        course.school_year,
      ].map(normalize).join(' ');
      return haystack.includes(term);
    };

    function renderCourses(data, { remember = true, emptyMessage = 'No courses found matching your search.' } = {}) {
      const list = Array.isArray(data) ? data : [];
      if (remember) {
        serverCourses = list;
      }

      container.innerHTML = '';

      if (list.length === 0) {
        container.innerHTML = `<div class="col-12 text-muted">${emptyMessage}</div>`;
        return;
      }

      list.forEach(course => {
        const card = document.createElement('div');
        card.className = 'col-md-4 mb-4 course-card';
        card.dataset.search = normalize(`${course.title} ${course.description} ${course.semester ?? ''} ${course.school_year ?? ''}`);
        card.innerHTML = `
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${course.title ?? 'Course'}</h5>
              <p class="card-text">Course Code: ${course.description ?? ''}</p>
              <small class="text-muted d-block">Semester/Term: ${course.semester ?? 'Not set'}</small>
              <small class="text-muted d-block">SY: ${course.school_year ?? 'TBD'}</small>
              <div class="mt-auto pt-2">
                <a href="<?= base_url('student/course/') ?>${course.id ?? course.course_id ?? ''}" class="btn btn-dark w-100">View Course</a>
              </div>
            </div>
          </div>
        `;
        container.appendChild(card);
      });
    }

    function handleClientFilter() {
      const term = normalize(searchInput.value);
      if (!term) {
        renderCourses(serverCourses, { remember: false, emptyMessage: 'No courses available.' });
        return;
      }
      const filtered = serverCourses.filter(course => matchesTerm(course, term));
      renderCourses(filtered, { remember: false });
    }

    if (searchInput && searchForm && container) {
      renderCourses(serverCourses, { emptyMessage: 'No courses available.' });

      searchInput.addEventListener('input', handleClientFilter);

      searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const term = searchInput.value.trim();

        fetch('<?= base_url("courses/search") ?>?search_term=' + encodeURIComponent(term), {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
          .then(resp => resp.ok ? resp.json() : Promise.reject())
          .then(data => {
            renderCourses(data, { emptyMessage: term ? 'No courses found matching your search.' : 'No courses available.' });
          })
          .catch(() => {
            container.innerHTML = '<div class="col-12 text-danger">Search failed. Please try again.</div>';
          });
      });
    }
  })();
</script>