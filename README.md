# Wellness Community Academy

Welcome to the Wellness Community Academy! This platform provides resources and support to individuals striving to improve their overall well-being through tailored programs, activities, and a vibrant community.

---

## Features
- Tailored wellness programs designed for diverse needs
- **Affiliate System**:
  - Affiliate registration with password creation
  - Tracking of direct and indirect referrals
  - Commission structure:
    - 15% for direct purchases
    - 2% for referred affiliate purchases
  - Dashboard for affiliates to monitor activities and earnings
- Progress tracking and analytics for personalized insights
- Educational resources and workshops on wellness topics
- Responsive design for mobile and desktop users

---

## Requirements
- **PHP**: 8.0 or higher
- **MySQL**: 8.0 or higher
- **Apache**: 2.4 or higher
- **Composer**: 2.x

---

## Installation
1. Clone the repository:
    ```bash
    git clone https://github.com/saintdannyyy/wellnesscommunityacademy.git
    ```
2. Navigate to the project directory:
    ```bash
    cd wellnesscommunityacademy
    ```
<!-- 3. Install dependencies using Composer:
    ```bash
    composer install
    ``` -->
3. Set up the environment file:
    ```bash
    cp .env.example .env
    ```
4. Configure the `.env` file with your database details:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=wellness_community
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```
5. Generate the application key:
    ```bash
    php artisan key:generate
    ```
6. Run the database migrations:
    ```bash
    php artisan migrate
    ```
7. (Optional) Seed the database with test data:
    ```bash
    php artisan db:seed
    ```

---

## Usage
1. Start the Apache server and ensure MySQL is running.
2. Open your browser and visit:
    - `http://localhost` to view the application.
    - `http://localhost/affiliates` to test the affiliate system.

---

## Contributing
We welcome contributions to enhance the project! Please read our [Contributing Guidelines](CONTRIBUTING.md) for more details.

---

## License
This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for more details.

---

## Contact
For inquiries or support, please email us at [support@wellnesscommunityacademy.com](mailto:danieltesl746@gmail.com).

---

### What's Next?
- Finalizing the affiliate system UI and backend integration
- Adding more wellness resources and workshops
- Enhancing progress tracking features
