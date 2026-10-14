#include<iostream>
using namespace std;

class solution{
    public:
    string removeOccurrances(string s,string part){
        while(s.length() > 0 && s.find(part) < s.length()){
            s.erace(s.find(part),part.length());
        }
        return 0;
    }
};
