async function loadPortfolio(){
    let res = await fetch('https://kartickiyf8tc.github.io/json/mydetails.json');
    let data = await res.json();
    let user = data[0];
    heroSection.innerHTML = `
        <img src="${user.image}" class="profile-img">
        <h1 class="name">${user.NAME}</h1>
        <p class="profession">${user.PROFESSION}</p>
    `;
    personalInfo.innerHTML = `
        <div class="info">Age : ${user.AGE}</div>
        <div class="info">DOB : ${user.dOB}</div>
        <div class="info">Gender : ${user.GENDER}</div>
        <div class="info">Email : ${user.EMAIL}</div>
        <div class="info">Phone : ${user.PHONE}</div>
        <div class="info">Address : ${user.ADDRESS}</div>
    `;
    skills.innerHTML = `
        <div class="tag">${Object.values(user.SKILLS)[0]}</div>
        <div class="tag">${Object.values(user.SKILLS)[1]}</div>
        <div class="tag">${Object.values(user.SKILLS)[2]}</div>
        <div class="tag">${Object.values(user.SKILLS)[3]}</div>
    `;
    hobbies.innerHTML = `
        <div class="tag">${Object.values(user.HOBBIES)[0]}</div>
        <div class="tag">${Object.values(user.HOBBIES)[1]}</div>
        <div class="tag">${Object.values(user.HOBBIES)[2]}</div>
    `;
    education.innerHTML = `
        <div class="edu-item">
            <h3>${user.EDUCATION.college.Degree}</h3>
            <p>${user.EDUCATION.college.University}</p>
            <p>${user.EDUCATION.college.Year}</p>
        </div>
        <div class="edu-item">
            <h3>${user.EDUCATION.school["High School"]}</h3>
            <p>${user.EDUCATION.school["Year of Passing"]}</p>
            <p>${user.EDUCATION.school.Mark}</p>
        </div>
    `;
}
loadPortfolio();