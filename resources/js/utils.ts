import dayjs from 'dayjs';

export function formatDate(date: string | null | undefined): string {
    if (!date) return '—';
    return dayjs(date).format('DD/MM/YYYY');
}

export function scoreClass(score: number | null, passingGrade: number): string {
    if (score === null) return 'text-slate-400';
    return score >= passingGrade ? 'text-green-600 font-medium' : 'text-red-500 font-medium';
}

export function avgClass(avg: number | null, passingGrade: number): string {
    if (avg === null) return 'text-slate-400';
    return avg >= passingGrade ? 'text-green-600' : 'text-red-500';
}
