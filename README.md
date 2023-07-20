Car Depo Inventory CMS
===============================

Introduction
------------

Car Inventory CMS is a Content Management System (CMS) designed to manage and display information about cars in an inventory. It allows two types of users: **Admin** and **Normal users**. Admin users have access to create, update, and delete cars, while normal users can view lists of cars, view car details, and add cars to their favorites (provided they are logged in). The CMS is built using PHP and MySQL and is hosted on the localhost using PHPMyAdmin and XAMPP server. This CMS was developed as a group project by Michael Ayesa, Momo, Paul, and Samil.

Requirements
------------

-   PHP 7.x or higher
-   MySQL database
-   XAMPP server (or any other local server)
-   PHPMyAdmin (or any other database management tool)

### Accessing the CMS

1.  Open your web browser and navigate to `http://localhost/path_to_project_folder` where `path_to_project_folder` is the directory where you placed the Car Inventory CMS files.
2.  The CMS homepage will be displayed, showing a list of available cars.

### Admin Actions

To access the admin functionalities, you need to log in as an admin user.

1.  Click on the **Login** link in the top-right corner of the homepage.
2.  Enter the admin email and password, provided during installation, into the login form and click **Login**.
3.  Once logged in as an admin, you will have access to additional functionalities in the CMS menu.

#### Create a New Car

1.  Click on **Dashboard** in the navigation menu.
2.  Select **Add New Car**.
3.  Fill in the necessary details of the car in the form provided (e.g., make, model, year, price, etc.).
4.  Click **Submit** to add the new car to the inventory.

#### Update Car Details

1.  Click on **Admin Dashboard** in the navigation menu.
2.  Select **Manage Cars**.
3.  Click on the **Edit** button next to the car you want to update.
4.  Make the necessary changes to the car details in the form provided.
5.  Click **Update** to save the changes.

#### Delete a Car

1.  Click on **Admin Dashboard** in the navigation menu.
2.  Select **Manage Cars**.
3.  Click on the **Delete** button next to the car you want to remove.
4.  Confirm the deletion when prompted.

### Normal User Actions

Normal users can view the list of available cars, view car details, and add cars to their favorites.

#### View Car List

1.  From the homepage, you can view the list of cars available in the inventory.
2.  Click on any car's **View Details** button to see more information about that car.

#### View Car Details

1.  Click on any car's **View Details** button either from the homepage or the car list.
2.  Detailed information about the car, including its make, model, year, price, and description, will be displayed.

#### Add Car to Favorites

1.  Log in as a normal user using the **Login** link.
2.  Click on any car's **Add to Favorites** button from the car list or car details page.
3.  The car will be added to your favorites list.

### Logout

1.  To log out, click on the **Logout** link in the top-right corner of the page.

Conclusion
----------

The Car Inventory CMS provides an efficient and user-friendly way to manage and display information about cars in an inventory. It allows admin users to create, update, and delete cars, while normal users can view car details, add cars to their favorites, and view the list of available cars. The system is built using PHP and MySQL, making it easy to set up and deploy on a local server using XAMPP and PHPMyAdmin. The project was successfully developed as a group effort by Michael Ayesa, Momo, Paul, and Samil.