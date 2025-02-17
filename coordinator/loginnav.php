<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JUW - FYP Progress Recorder | Dashboard</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: rgb(236, 236, 236);
    }
    .header {
      background-color: #051747;
    height: 60px;
    display: flex;
    justify-content: center;
    padding: 0 20px;
    flex-wrap: nowrap;
    }
    .header-title {
      color: #fff;
      font-size: 24px;
      font-weight: bold;
      text-align: center;
    }
    .sidebar {
      position: fixed;
      top: 60px;
      left: -250px;
      height: calc(100% - 60px);
      width: 250px;
      background-color: #fff;
      transition: left 0.3s ease-in-out;
    }
    .sidebar.show {
      left: 0;
    }
    .sidebar a {
      padding: 15px 20px;
      display: block;
      color: #051747;
      font-size: 16px;
      font-weight: bold;
      text-decoration: none;
      border-bottom: 0.3px solid #ccc;
    }
    .sidebar a:first-child {
      border-top: 0.5px solid #ccc;
    }
    .sidebar a:hover {
      background-color: #f1f1f1;
    }
    .toggle-btn {
      cursor: pointer;
      color: #fff;
    }
    .user-name {
      color: #fff;
      font-size: 16px;
      position: relative;
    }
    
    /* Transition for sidebar and content */
    .sidebar, #content {
      transition: left 0.3s ease-in-out, margin-left 0.3s ease-in-out;
    }
    .recent{
      border: 1px solid #ccc;
      box-shadow: 2px 3px 7px gray;
      border-radius: 10px;
      padding: 15px;
      margin: 15px 0px;
      background-color: #D9D9D9;
    }
    .recent-content  {
      color: #081F62;
      font-size: 25px;
      padding-left: 15px;
    }
    .fa-chart-line{
      color: #081F62;
      font-size: 27px;
    }
    /* Custom grid styles */
    .dashbtnparent {
      justify-content: center;
    }
    .dashbtn{
      border: 1px solid #ccc;
      box-shadow: 2px 3px 7px gray;
      border-radius: 10px;
      padding: 15px 0px;
      margin: 7px;
      text-align: center;
    }
    @media (max-width: 768px) {
      .header-title {
        font-size: 20px; /* Decrease font size on smaller screens */
      }
      .sidebar {
        width: 200px;
      }
      .container-fluid {
        padding-left: 0; /* Remove left padding */
        padding-right: 0; /* Remove right padding */
      }
    }
    @media (min-width: 768px){
      .row-cols-md-4>* {
          -ms-flex: 0 0 25%;
          flex: 0 0 25%;
          max-width: 23%;
      }
    }
  </style>
</head>
<body>
 
<div class="navbar header sticky-top">
<img src="Icons/logo.png" alt="Logo" class="header-logo p-2" />
  <div class="header-title">FYP Progress Recorder</div>
</div>
