// short relative time like "5m", "3h", "2d"
export function timeAgo(value) {
    if (!value) {
        return '';
    }

    const seconds = Math.floor((Date.now() - new Date(value).getTime()) / 1000);

    if (seconds < 60) {
        return 'now';
    }

    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) {
        return minutes + 'm';
    }

    const hours = Math.floor(minutes / 60);
    if (hours < 24) {
        return hours + 'h';
    }

    const days = Math.floor(hours / 24);
    if (days < 7) {
        return days + 'd';
    }

    return Math.floor(days / 7) + 'w';
}

// e.g. "March 5" or "March 5, 2024" when it's not this year
export function formatDate(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    const options = { month: 'long', day: 'numeric' };

    if (date.getFullYear() !== new Date().getFullYear()) {
        options.year = 'numeric';
    }

    return date.toLocaleDateString('en-US', options);
}

export function formatTime(value) {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
    });
}
