let track_list = document.getElementById('track_list');

let i = 4;

document.getElementById('add_more_tracks').addEventListener('click', function() {
    i = i + 1;
    track_list.insertAdjacentHTML('beforeend', `
    <div class="track_info">
        <div class="input_labels">
            <label>Track Nr.</label>
            <input type="number" class="track_nr" name="tracks[ ${i+1} ][position]">
        </div>
        <div class="input_labels">
            <label>Author</label>
            <input type="text" class="author" name="tracks[ ${i+1} ][artist]">
        </div>
        <div class="input_labels">
            <label>Title</label>
            <input type="text" class="title" name="tracks[ ${i+1} ][song_title]">
        </div>
        <div class="input_labels">
            <label>Duration</label>
            <input type="text" class="duration" name="tracks[ ${i+1} ][duration]">
        </div>
    </div>
    `);

});