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
        <div class="error_box"[ ${i+1} ]></div>
    </div>
    `);
});

    document.getElementById('add_album_with_tracks').addEventListener('submit', function (e) {
    e.preventDefault();
    
    const trackRows = document.querySelectorAll('.track_info');
    const errorBox = document.getElementsByClassName('error_box');

    for (let i = 0; i < errorBox.length; i++) {
        errorBox[i].innerHTML = '';
    }
    let hasErrors = false;
    let focused = false;

    trackRows.forEach((row, index) => {
        const inputs = row.querySelectorAll('input');
        const values = [...inputs].map(i => i.value.trim());

        const anyFilled = values.some(v => v !== '');
        const allFilled = values.every(v => v !== '');

        trackRows.forEach(i => i.classList.remove('error_box'));

        if (anyFilled && !allFilled) {
            hasErrors = true;
             console.log(index);
             errorBox[index].insertAdjacentHTML(
                        'beforeend',
                        `<p>Track is incomplete. Fill all fields or leave it empty.</p>`
                    );

            inputs.forEach(input => {
                if (!input.value.trim()) {

                    if (!focused) {
                        input.focus();
                        focused = true;
                    }
                }
            });
           
        }
    });

    if (!hasErrors) {
        this.submit();
    }
});


