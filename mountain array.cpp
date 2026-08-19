class Solution {
public:
    int peakIndexInMountainArray(vector<int>& arr) {
        int st = 1;
        int end = arr.size() - 2;

        while (st <= end) {
            int mid = st + (end - st) / 2;

            // Peak element found
            if (arr[mid - 1] < arr[mid] && arr[mid] > arr[mid + 1]) {
                return mid;
            }
            // We are on the increasing side
            else if (arr[mid] < arr[mid + 1]) {
                st = mid + 1;
            }
            // We are on the decreasing side
            else {
                end = mid - 1;
            }
        }

        return -1;
    }
};