<?php

namespace App\Helpers;

class MediaHelper
{
    /**
     * Detects media type from a URL.
     */
    public static function detectMediaType(string $url): string
    {
        $url = trim($url);
        if (preg_match('~(youtube\.com|youtu\.be)~i', $url)) {
            return 'YouTube';
        }
        if (preg_match('~(tiktok\.com)~i', $url)) {
            return 'TikTok';
        }
        if (preg_match('~(instagram\.com)~i', $url)) {
            return 'Instagram';
        }
        if (preg_match('~\.(jpe?g|png|gif|webp|svg)(\?.*)?$~i', $url)) {
            return 'Image';
        }
        return 'OtherLink';
    }

    /**
     * Renders responsive HTML embed or preview for a media item.
     *
     * @param array $media [id, media_type, media_url, file_path]
     * @return string HTML
     */
    public static function renderEmbed(array $media): string
    {
        $type = $media['media_type'];
        $url = htmlspecialchars($media['media_url'], ENT_QUOTES, 'UTF-8');
        $filePath = !empty($media['file_path']) ? htmlspecialchars($media['file_path'], ENT_QUOTES, 'UTF-8') : null;

        switch ($type) {
            case 'YouTube':
                $youtubeId = self::extractYouTubeId($media['media_url']);
                if ($youtubeId) {
                    return '<div class="media-embed media-youtube">
                        <iframe 
                            src="https://www.youtube-nocookie.com/embed/' . htmlspecialchars($youtubeId, ENT_QUOTES, 'UTF-8') . '" 
                            title="YouTube video player" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen 
                            loading="lazy">
                        </iframe>
                    </div>';
                }
                return '<div class="media-card-link">
                    <a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        Tonton di YouTube
                    </a>
                </div>';

            case 'TikTok':
                $tiktokId = self::extractTikTokId($media['media_url']);
                return '<div class="media-embed media-tiktok">
                    <blockquote class="tiktok-embed" cite="' . $url . '" data-video-id="' . htmlspecialchars($tiktokId ?? '', ENT_QUOTES, 'UTF-8') . '" style="max-width: 605px;min-width: 325px;">
                        <section>
                            <a target="_blank" title="Tonton di TikTok" href="' . $url . '">Lihat Konten di TikTok</a>
                        </section>
                    </blockquote>
                </div>';

            case 'Instagram':
                $igCode = self::extractInstagramCode($media['media_url']);
                if ($igCode) {
                    return '<div class="media-embed media-instagram">
                        <iframe 
                            src="https://www.instagram.com/p/' . htmlspecialchars($igCode, ENT_QUOTES, 'UTF-8') . '/embed/" 
                            frameborder="0" 
                            scrolling="no" 
                            allowtransparency="true" 
                            loading="lazy">
                        </iframe>
                    </div>';
                }
                return '<div class="media-card-link">
                    <a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        Buka di Instagram
                    </a>
                </div>';

            case 'Image':
                $imgSrc = $filePath ? '/uploads/' . $filePath : $media['media_url'];
                return '<div class="media-image-wrapper">
                    <img src="' . htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') . '" alt="Foto lokasi" class="media-img" loading="lazy">
                </div>';

            default:
                return '<div class="media-card-link">
                    <a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        Buka Tautan Terkait
                    </a>
                </div>';
        }
    }

    public static function extractYouTubeId(string $url): ?string
    {
        if (preg_match('~(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})~i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public static function extractTikTokId(string $url): ?string
    {
        if (preg_match('~/video/(\d+)~i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public static function extractInstagramCode(string $url): ?string
    {
        if (preg_match('~instagram\.com\/(?:p|reel|tv)\/([a-zA-Z0-9_-]+)~i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
