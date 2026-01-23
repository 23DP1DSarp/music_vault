let p=document.getElementById("track_list"),t=4;document.getElementById("add_more_tracks").addEventListener("click",function(){t=t+1,p.insertAdjacentHTML("beforeend",`
    <div class="track_info">
        <div class="input_labels">
            <label>Track Nr.</label>
            <input type="number" class="track_nr" name="tracks[ ${t+1} ][position]">
        </div>
        <div class="input_labels">
            <label>Author</label>
            <input type="text" class="author" name="tracks[ ${t+1} ][artist]">
        </div>
        <div class="input_labels">
            <label>Title</label>
            <input type="text" class="title" name="tracks[ ${t+1} ][song_title]">
        </div>
        <div class="input_labels">
            <label>Duration</label>
            <input type="text" class="duration" name="tracks[ ${t+1} ][duration]">
        </div>
    </div>
    `),document.getElementById("albumForm").addEventListener("submit",function(n){n.preventDefault();const c=document.querySelectorAll(".track_info"),a=document.getElementById("trackErrors");a.innerHTML="";let s=!1,r=!1;c.forEach((o,d)=>{const l=o.querySelectorAll("input"),i=[...l].map(e=>e.value.trim()),u=i.some(e=>e!==""),m=i.every(e=>e!=="");l.forEach(e=>e.classList.remove("input-error")),u&&!m&&(s=!0,l.forEach(e=>{e.value.trim()||(e.classList.add("input-error"),r||(e.focus(),r=!0))}),a.insertAdjacentHTML("beforeend",`<p>⚠️ Track #${d+1} is incomplete. Fill all fields or leave it empty.</p>`))}),s||this.submit()})});
