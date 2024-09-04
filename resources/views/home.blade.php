@extends('layouts.app')

@section('content')
<div class="container">
    <style>
        .blog-section {
    background-color: #f4f4f4;
    padding: 20px;
}

.blog-container {
    display: flex;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.user-image {
    flex: 0 0 150px;
    padding: 10px;
}

.user-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.blog-details {
    flex: 1;
    padding: 20px;
}

.blog-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.blog-header h2 {
    margin: 0;
    font-size: 24px;
    color: #059669;
}

.rating {
    display: flex;
    align-items: center;
}

.stars {
    color: #f5c518;
    font-size: 20px;
}

.rating-text {
    margin-left: 5px;
    font-size: 16px;
    color: #333;
}

.verified-tag {
    background-color: #059669;
    color: #fff;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}

.blog-buttons {
    margin-top: 20px;
}

.btn {
    background-color: #059669;
    color: #fff;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 4px;
    margin-right: 10px;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #047a5e;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .blog-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .user-image {
        margin-bottom: 10px;
    }
}
    </style>
    <section class="blog-section">
        <div class="blog-container">
            <!-- User Image and Details -->
            <div class="user-image">
                <img src="{{ asset('app-assets/images/default_img.jpg') }}" alt="User Image">
            </div>
            <div class="blog-details">
                <div class="blog-header">
                    <h2>John Doe</h2>
                    <div class="rating">
                        <span class="stars">
                            <!-- Example for 4 stars -->
                            ★★★★☆
                        </span>
                        <span class="rating-text">(4/5)</span>
                    </div>
                    <span class="verified-tag">Verified</span>
                </div>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam a sapien urna.</p>
                <div class="blog-buttons">
                    <a href="#more-detail" class="btn">View More Detail</a>
                    <a href="#contact" class="btn">Contact Me</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
